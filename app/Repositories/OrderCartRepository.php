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
use Illuminate\Http\Request;
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

    public function old_place_order($uuid){
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
            'CustomerID' => intval($user->g5_id)
        ];

        $orderId = $g5->newOrder($orderData);

        $sel_items = [];
        foreach($order->order_cart_items as $item){
            $menu = $this->menu->find($item->food_menu_id);
            $sel_items[] = [
                'ItemID' => intval($menu->g5_id),
                'Quantity' => intval($item->quantity),
                'UsedPrice' => $item->unit_price,
                'CustomerNumber' => intval($user->g5_id),
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
                        'ItemID' => intval($addOn->g5_id),
                        'Quantity' => intval($add_on->quantity),
                        'UsedPrice' => $add_on->unit_price,
                        'CustomerNumber' => intval($user->g5_id),
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
        $order->status = 'Pending';
        $order->open = 0;
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
            'is_user_credited' => false,
            'payment_processor' => 'G5 POS',
            'external_reference' => $order->g5_id
        ]);

        return $order;
    }

    public function place_order($data, User $user){
        $cart = OrderCart::create([
            'uuid' => Str::uuid().'-'.time(),
            'user_id' => $user->id,
            'user_name' => $user->name,
            'order_type' => $data['order_type'],
            'delivery_address' => (isset($data['delivery_address']) and !empty($data['delivery_address'])) ? $data['delivery_address'] : null,
            'longitude' => (isset($data['longitude']) and !empty($data['longitude'])) ? $data['longitude'] : null,
            'latitude' => (isset($data['latitude']) and !empty($data['latitude'])) ? $data['latitude'] : null,
            ''
        ]);
    }

    public function user_place_order(Request $request, $uuid)
    {
        $order = $this->findFirstBy(['uuid' => $uuid, 'user_id' => auth('user-api')->user()->id]);
        if(empty($order)){
            $this->errors = "No Order was fetched";
            return false;
        }

        $confirm = $this->place_order($order->uuid);
        if(!$confirm){
            return false;
        }

        $data = $request->all();
        $data['order_by_type'] = "user";
        $data['order_by_id'] = auth('user-api')->user()->id;
        $data['time_ordered'] = Carbon::now('Africa/Lagos')->format('Y-m-d H:i:s');
       
        $confirm->update($data);

        parent::__construct(new OrderTracker());
        $this->create([
            'order_cart_id' => $confirm->id,
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
            'order_cart_id' => $order->id,
            'status' => 'Pending',
            'agent_type' => 'admin',
            'agent_id' => auth('user-api')->user()->id
        ]);

        return $order;
    }

    public function index($limit = 10, $search = "")
    {
        $criteria = [
            ['status', '!=', 'Delivered']
        ];
        if(!empty($search)){
            $criteria[] = ['order_number', 'like', '%'.$search.'%'];
        }
        $orderBy = [
            ['created_at', 'asc']
        ];

        $orders = $this->findBy($criteria, $orderBy, $limit);
        return $orders;
    }

    public function completed_orders($limit = 10, $search = "")
    {
        $criteria = [
            ['status', '=', 'Delivered']
        ];
        if(!empty($search)){
            $criteria[] = ['order_number', 'like', '%'.$search.'%'];
        }
        $orderBy = [
            ['created_at', 'desc']
        ];

        $orders = $this->findBy($criteria, $orderBy, $limit);
        return $orders;
    }

    public function user_index($limit = 10)
    {
        $criteria = [
            ['user_id', '=', auth('user-api')->user()->id],
            ['status', '!=', 'Delivered']
        ];
        $orderBy = [
            ['created_at', 'asc']
        ];
        $orders = $this->findBy($criteria, $orderBy, $limit);
        return $orders;
    }

    public function user_completed_orders($limit = 10)
    {
        $criteria = [
            ['user_id', '=', auth('user-api')->user()->id],
            ['status', '=', 'Delivered']
        ];
        $orderBy = [
            ['created_at', 'desc']
        ];
        $orders = $this->findBy($criteria, $orderBy, $limit);
        return $orders;
    }

    public function show($uuid){
        return $this->findByUuid($uuid);
    }

    public function change_status($uuid, $status)
    {
        $order = $this->findByUuid($uuid);
        if(empty($order)){
            $this->errors = "No Order was fetched";
            return false;
        }

        $order->status = $status;
        $order->save();

        parent::__construct(new OrderTracker());
        $this->create([
            'order_cart_id' => $order->id,
            'status' => $status,
            'agent_type' => 'admin',
            'agent_id' => auth('admin-api')->user()->id
        ]);

        return $order;
    }
} 