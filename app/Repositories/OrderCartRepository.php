<?php

namespace App\Repositories;

use App\Models\FoodMenu;
use App\Models\OrderCart;
use App\Models\OrderTracker;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Repositories\Interfaces\OrderCartRepositoryInterface;
use App\Services\G5PosService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OrderCartRepository extends AbstractRepository implements OrderCartRepositoryInterface
{
    public $errors = "";
    public $menu;

    public function __construct(OrderCart $cart)
    {
        parent::__construct($cart);
        $this->menu = new FoodMenuRepository(new FoodMenu());
    }

    public function place_order($uuid){
        $order = $this->findByUuid($uuid);
        if(empty($order)){
            $this->errors = "No Order was fetched";
            return false;
        }
        if($order->status != 'Checkout'){
            $this->errors = "Cannot Confirm this Order";
            return false;
        }

        $g5 = new G5PosService();
        $employee_code = config('g5pos.api_credentials.order_employee_code');
        $getNumber = $g5->getOrderNumber();
        $orderNumber = $getNumber[0]['OrderNumber'];

        $user = $order->user;

        $orderData = [
            'OrderNumber' => intval($orderNumber),
            'OrderMenuID' => 2,
            'UserID' => intval($employee_code),
            'CustomerID' => $user->g5_id
        ];

        $orderId = $g5->newOrder($orderData);

        $sel_items = [];
        foreach($order->order_cart_items as $item){
            $menu = $this->menu->find($item->food_menu_id);
            $sel_items[] = [
                'ItemID' => $menu->g5_id,
                'Quantity' => $item->quantity,
                'UsedPrice' => $item->unit_price * $item->quantity,
                'CustomerNumber' => $user->g5_id,
                'AffectedItem' => 0,
                'VoidReasonID' => 0,
                'Status' => 'selected',
                'OrderbyEmployeeId' => intval($employee_code),
                'PriceModeID' => 1,
                'OrderingTime' => Carbon::now('Africa/Lagos')->format('Y-m-d'),
                'ItemDescription' => $menu->name,
                'ItemRemark' => '',
                'inctax' => 0,
                'SetMenu' => false
            ];

            if(!empty($item->add_ons)){
                $add_ons = json_decode($item->add_ons);
                foreach($add_ons as $add_on){
                    $addOn = $this->menu->find($add_on->id);
                    $sel_items[] = [
                        'ItemID' => $addOn->g5_id,
                        'Quantity' => $add_on->quantity,
                        'UsedPrice' => $add_on->total_price,
                        'CustomerNumber' => $user->g5_id,
                        'AffectedItem' => 0,
                        'VoidReasonID' => 0,
                        'Status' => 'selected',
                        'OrderbyEmployeeId' => intval($employee_code),
                        'PriceModeID' => 1,
                        'OrderingTime' => Carbon::now('Africa/Lagos')->format('Y-m-d'),
                        'ItemDescription' => $addOn->name,
                        'ItemRemark' => '',
                        'inctax' => 0,
                        'SetMenu' => false
                    ];
                }
            }
        }

        $saveData = [
            'OrderID' => intval($orderId),
            'selectedItems' => $sel_items
        ];
        $res = $g5->saveOrder($saveData);

        if(!filter_var($res, FILTER_VALIDATE_BOOLEAN)){
            $this->errors = "Order can't be processed";
            return false;
        }

        $order->g5_id = $orderId;
        $order->g5_order_number = $orderNumber;
        $order->save();

        if($order->tip_amount > 0){
            try {
                $tip = [
                    'order' => $order->g5_id,
                    'Amount' => $order->tip_amount,
                    'PaymentTypeId' => 4,
                    'PayAmt' => $order->total_amount,
                ];

                $g5->tip($tip);
            } catch(\Exception $e){
                Log::error('Tip Error - '.$order->id.': '.$e->getMessage());
            }
        }

        if(empty($user->parent_id)){
            $wallet = Wallet::where('user_id', $user->id)->first();
        } else {
            $wallet = Wallet::where('user_id', $user->parent_id)->first();
        }

        $wallet->balance -= $order->total_amount;
        $wallet->save();

        WalletTransaction::create([
            'wallet_id' => $wallet->id,
            'amount' => $order->total_amount,
            'type' => 'Debit',
            'uuid' => Str::uuid().'-'.time(),
            'is_user_created' => false,
            'payment_processor' => 'G5 POS',
            'externake_reference' => $order->g5_id
        ]);

        return $order;
    }

    public function user_place_order($uuid)
    {
        $order = $this->findFirstBy(['uuid' => $uuid, 'user_id', auth('user-api')->user()->id]);
        if(empty($order)){
            $this->errors = "No Order was fetched";
            return false;
        }

        $confirm = $this->place_order($order->uuid);
        if(!$confirm){
            return false;
        }

        $confirm->order_by_type = "user";
        $confirm->order_by_id = auth('user-api')->user()->id;
        $confirm->save();

        parent::__construct(new OrderTracker());
        $this->create([
            'order_cart_id',
            'status' => 'Pending',
            'agent_type' => 'user',
            'agent_id' => auth('user-api')->user()->id
        ]);

        return $confirm;
    }

    public function admin_place_order($uuid, User $user)
    {
        $order = $this->place_order($uuid);
        if(!$order){
            return false;
        }

        $order->order_by_type = "admin";
        $order->order_by_id = auth('user-api')->user()->id;

        parent::__construct(new OrderTracker());
        $this->create([
            'order_cart_id',
            'status' => 'Pending',
            'agent_type' => 'user',
            'agent_id' => auth('user-api')->user()->id
        ]);

        return $order;
    }
} 