<?php

namespace App\Repositories;

use App\Models\Admin;
use App\Models\FoodMenu;
use App\Models\OrderCart;
use App\Models\OrderCartItem;
use App\Models\OrderTracker;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Repositories\Interfaces\OrderCartRepositoryInterface;
use App\Services\AuthService;
use App\Services\G5PosService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OrderCartRepository extends AbstractRepository implements OrderCartRepositoryInterface
{
    public $errors = "";
    public $menu;
    public $cart_item;

    public function __construct(OrderCart $cart)
    {
        parent::__construct($cart);
        $this->cart_item = new OrderCartItemRepository(new OrderCartItem());
        $this->menu = new FoodMenuRepository(new FoodMenu());
    }

    public function place_order($data, User $user){
        $delivery_fee = (isset($data['delivery_fee_uuid']) and !empty($data['delivery_fee_uuid'])) ? (!empty($this->menu->findByUuid($data['delivery_fee_uuid'])) ? $this->findByUuid($data['delivery_fee_uuid']) : null) : null;
        $cart = OrderCart::create([
            'uuid' => Str::uuid().'-'.time(),
            'user_id' => $user->id,
            'user_name' => $user->firstname.' '.$user->lastname,
            'order_type' => $data['order_type'],
            'delivery_address' => (isset($data['delivery_address']) and !empty($data['delivery_address'])) ? $data['delivery_address'] : null,
            'longitude' => (isset($data['longitude']) and !empty($data['longitude'])) ? $data['longitude'] : null,
            'latitude' => (isset($data['latitude']) and !empty($data['latitude'])) ? $data['latitude'] : null,
            'delivery_phone' => (isset($data['delivery_phone']) and !empty($data['delivery_phone'])) ? $data['delivery_phone'] : null,
            'delivery_email' => (isset($data['delivery_email']) and !empty($data['delivery_email'])) ? $data['delivery_email'] : null,
            'status' => "Pending",
            'delivery_fee_id' => ($delivery_fee == null) ? null : $delivery_fee->id,
            'delivery_amount' => ($delivery_fee == null) ? 0 : $delivery_fee->amount,
            'tip_amount' => (isset($data['tip_amount']) and !empty($data['tip_amount'])) ? $data['tip_amount'] : 0,
            'open' => 1,
            'order_by_type' => $data['order_by_type'],
            'order_by_id' => $data['order_by_id'],
            'time_ordered' => Carbon::now('Africa/Lagos')->format('Y-m-d H:i:s')
        ]);

        $errors = [];

        foreach($data['items'] as $item){
            $item['user_id'] = $user->id;
            $item['order_cart_id'] = $cart->id;
            $store = $this->cart_item->store($item);
            if(!$store){
                $errors[] = $this->cart_item->errors;
            }
        }

        if(!empty($errors)){
            $this->errors = $errors;
            return false;
        }

        $cart->open = 0;
        $cart->save();

        $this->track_order($cart, 'Pending', $cart->order_by_type, $cart->order_by_id);

        return $this->find($cart->id);        
    }

    public function modify_order(string $uuid, Request $request)
    {
        $data = $request->all();
        $order = $this->findByUuid($uuid);
        if(empty($order)){
            $this->errors = "No Order was fetched";
            return false;
        }

        if($order->status != 'Pending'){
            $this->errors = "You can only Modify a Pending Order";
            return false;
        }

        $delivery_fee = (isset($data['delivery_fee_uuid']) and !empty($data['delivery_fee_uuid'])) ? (!empty($this->findByUuid($data['delivery_fee_uuid'])) ? $this->findByUuid($data['delivery_fee_uuid']) : null) : null;
        $order->update([
            'order_type' => $data['order_type'],
            'delivery_address' => (isset($data['delivery_address']) and !empty($data['delivery_address'])) ? $data['delivery_address'] : null,
            'longitude' => (isset($data['longitude']) and !empty($data['longitude'])) ? $data['longitude'] : null,
            'latitude' => (isset($data['latitude']) and !empty($data['latitude'])) ? $data['latitude'] : null,
            'delivery_phone' => (isset($data['delivery_phone']) and !empty($data['delivery_phone'])) ? $data['delivery_phone'] : null,
            'delivery_email' => (isset($data['delivery_email']) and !empty($data['delivery_email'])) ? $data['delivery_email'] : null,
            'status' => "Pending",
            'delivery_fee_id' => ($delivery_fee == null) ? null : $delivery_fee->id,
            'delivery_amount' => ($delivery_fee == null) ? 0 : $delivery_fee->amount,
            'tip_amount' => (isset($data['tip_amount']) and !empty($data['tip_amount'])) ? $data['tip_amount'] : 0,
            'open' => 1
        ]);

        foreach($order->order_cart_items as $cart_item){
            $this->cart_item->remove_item($cart_item->uuid);
        }

        foreach($data['items'] as $item){
            $item['user_id'] = $order->user_id;
            $item['order_cart_id'] = $order->id;
            $this->cart_item->store($item);
        }

        $order->open = 0;
        $order->save();

        return $this->find($order->id);
    }

    public function confirm_order(Request $request, $uuid){
        $order = $this->findByUuid($uuid);
        if(empty($order)){
            $this->errors = "No Order was fetched";
            return false;
        }

        if($order->status != 'Pending'){
            $this->errors = "You can only Modify a Pending Order";
            return false;
        }

        if(!empty($request->delivery_fee_uuid)){
            $delivery_fee = $this->menu->findByUuid($request->delivery_fee_uuid);
            $order->delivery_fee_id = !empty($delivery_fee) ? $delivery_fee->id : null;
            $order->delivery_amount = !empty($delivery_fee) ? $delivery_fee->amount : 0;
            $order->total_amount = $order->item_amount + $order->delivery_amount + $order->service_charge_amount + $order->tip_amount;
            $order->save();
        }

        if(env('APP_ENV') == 'production'){
            if(!$this->g5_place($order)){
                return false;
            }
        } else {
            $order->g5_id = 'TEST'.$order->id;
            $order->g5_order_number = 'TEST'.$order->id;
            $order->save();
            
            $wallet = $order->user->wallet;
            $wallet->balance -= $order->total_amount;
            $wallet->save();

            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'amount' => $order->total_amount,
                'type' => 'Debit',
                'uuid' => Str::uuid().'-'.time(),
                'is_user_credited' => false,
                'reason' => 'Order',
                'reason_id' => $order->id,
                'payment_processor' => 'G5 POS',
                'external_reference' => $order->g5_id
            ]);
        }

        $order = $this->change_status($order->uuid, "Processing");

        return $order;
    }

    public function g5_place(OrderCart $order){
        if($order->status != "Pending"){
            $this->errors = "Cannot Confirm this Order";
            return false;
        }

        $g5 = new G5PosService();
        $employee_code = config('g5pos.api_credentials.order_employee_code');
        $getNumber = $g5->getOrderNumber();
        $orderNumber = $getNumber[0]['OrderNumber'];

        $user = $order->user;
        if(!empty($user->parent_id)){
            $user = User::find($user->parent_id);
        }

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
            if($item->modifier_price > 0){
                $modifier = $this->menu->find($item->modifier_id);
                $sel_items[] = [
                    'ItemID' => intval($modifier->g5_id),
                    'Quantity' => 1,
                    'UsedPrice' => $item->modifier_price,
                    'CustomerNumber' => intval($user->g5_id),
                    'AffectedItem' => 0,
                    'VoidReasonID' => 0,
                    'Status' => 'selected',
                    'OrderbyEmployeeId' => intval($employee_code),
                    'PriceModeID' => 1,
                    'OrderingTime' => Carbon::now('Africa/Lagos')->format('Y-m-d'),
                    'ItemDescription' => $modifier->name,
                    'ItemRemark' => '',
                    'inctax' => 0,
                    'SetMenu' => false
                ];
            }
        }
        if(!empty($order->delivery_fee_id)){
            $fee = FoodMenu::find($order->delivery_fee_id);
            $sel_items[] = [
                'ItemID' => intval($fee->g5_id),
                'Quantity' => 1,
                'UsedPrice' => $fee->amount,
                'CustomerNumber' => intval($user->g5_id),
                'AffectedItem' => 0,
                'VoidReasonID' => 0,
                'Status' => 'selected',
                'OrderbyEmployeeId' => intval($employee_code),
                'PriceModeID' => 1,
                'OrderingTime' => Carbon::now('Africa/Lagos')->format('Y-m-d'),
                'ItemDescription' => $fee->name,
                'ItemRemark' => '',
                'inctax' => 0,
                'SetMenu' => false
            ];
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

        $wallet = $user->wallet;
        $wallet->balance -= $order->total_amount;
        $wallet->save();

        WalletTransaction::create([
            'wallet_id' => $wallet->id,
            'amount' => $order->total_amount,
            'type' => 'Debit',
            'uuid' => Str::uuid().'-'.time(),
            'is_user_credited' => false,
            'reason' => 'Order',
            'reason_id' => $order->id,
            'payment_processor' => 'G5 POS',
            'external_reference' => $order->g5_id
        ]);

        return true;
    }

    public function track_order(OrderCart $order, $status, $agent, $agent_id) : void
    {
        $order->status = $status;
        $order->save();

        OrderTracker::create([
            'order_cart_id' => $order->id,
            'status' => $status,
            'agent_type' => $agent,
            'agent_id' => $agent_id
        ]);
    }

    public function user_place_order(Request $request)
    {
        $user = auth('user-api')->user();
        $all = $request->all();
        $all['order_by_type'] = 'user';
        $all['order_by_id'] = $user->id;

        $order = $this->place_order($all, $user);

        return $order;
    }

    public function admin_place_order(Request $request, Admin $admin)
    {
        $all = $request->all();
        $all['order_by_type'] = 'admin';
        $all['order_by_id'] = $admin->id;

        $user = User::where('uuid', $request->user_id)->first();

        $order = $this->place_order($all, $user);

        return $order;
    }

    public function index($limit = 10, $search = "")
    {
        $criteria = [
            ['status', '!=', 'Completed']
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
            ['status', '=', 'Completed']
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
            ['status', '!=', 'Completed']
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
            ['status', '=', 'Completed']
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
        $order->open = ($order->status == 'Pending') ? 1 : 0;
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