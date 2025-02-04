<?php

namespace App\Repositories;

use App\Jobs\SaveOrderCartJob;
use App\Jobs\SaveOrderJob;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use App\Services\G5PosService;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OrderRepository extends AbstractRepository implements OrderRepositoryInterface
{
    public $errors;

    public function __construct(Order $order)
    {
        parent::__construct($order);
    }

    public function fetch_g5_order($user_id){
        try {
            $user = User::find($user_id);
            if(empty($user)){
                return;
            }

            $from_date = !empty($user->last_synced) ? date('Y-m-d', strtotime($user->last_synced)) : '2000-01-01';

            $g5_id = $user->g5_id;
            $g5 = new G5PosService();

            $orders = $g5->getOrders([
                'CustomerID' => $g5_id,
                'FromDate' => $from_date,
                'ToDate' => date('Y-m-d')
            ]);
            $orders = json_decode($orders, true);

            foreach($orders as $order){
                SaveOrderCartJob::dispatch($order, $user);
            }
            $user->last_synced = date('Y-m-d H:i:s');
            $user->next_order_sync = date('Y-m-d H:i:s', time() + (60 * 60 * 6));
            $user->save();
        } catch (Exception $e){
            Log::error($e->getMessage());
            return false; 
        }
    }

    public function index($limit=4){
        $data = [
            ['user_id', auth('user-api')->user()->id],
            ['delivery_status', '!=', 'Delivered']
        ];
        $orderBy = [
            ['date_ordered', 'desc']
        ];
        $orders = $this->findBy($data, $orderBy, $limit);
        
        if(auth('user-api')->user()->next_order_sync <= date('Y-m-d H:i:s')){
            $this->fetch_g5_order(auth('user-api')->user()->id);
        }
        
        return $orders;
    }

    public function past_orders($limit=15){
        $data = [
            'user_id' => auth('user-api')->user()->id,
            'delivery_status' => 'Delivered'
        ];
        $orderBy = [
            ['date_ordered', 'desc']
        ];

        $orders = $this->findBy($data, $orderBy, $limit);
        return $orders;
    }

    public function make_order(array $data){
        $service = new G5PosService();
        $employee_code = config('g5pos.api_credentials.order_employee_code');
        $getNumber = $service->getOrderNumber();
        $orderNumber = intval($getNumber[0]['OrderNumber']);

        $orderData = [
            'OrderNumber' => $orderNumber,
            'OrderMenuID' => 2,
            'UserID' => intval($employee_code),
            'CustomerID' => $data['g5_id']
        ];

        $orderId = $service->newOrder($orderData);

        $selectedItems = [];
        foreach($data['orders'] as $item){
            $selectedItems[] = [
                "ItemID" => intval($item['g5_id']),
                "Quantity" => $item['quantity'],
                "UsedPrice" => $item['amount'],
                "CustomerNumber" => $data['g5_id'],
                "AffectedItem" => 0,
                "VoidReasonID" => 0,
                "Status" => "selected",
                "OrderbyEmployeeId" => intval($employee_code), //intval(env("G5POS_EMPLOYEE_CODE")),
                "PriceModeID" => 1,
                "OrderingTime" => date('Y-m-d'),
                "ItemDescription" => $item['name'],
                "ItemRemark" => "",
                "inctax" => 0,
                "SetMenu" => false
            ];
        }

        $saveData = [
            'OrderID' => intval($orderId),
            'selectedItems' => $selectedItems
        ];
        $response = $service->saveOrder($saveData);

        if(!filter_var($response, FILTER_VALIDATE_BOOLEAN)){
            $this->errors = "Order can't be processed";
            return false;
        }

        $data['g5_id'] = $orderId;
        $data['g5_order_number'] = $orderNumber;
        $data['date_ordered']  = Carbon::now();
        $data['uuid'] = Str::uuid().'-'.time();
        $order = $this->create($data);

        $details = $service->getOrderDetails(['OrderID' => $order->g5_id]);

        $employeeName = null;

        foreach(json_decode($details, true) as $detail){
            OrderItem::create([
                'uuid' => Str::uuid().'-'.time(),
                'order_id' => $order->id,
                'quantity' => $detail['Quantity'],
                'amount' => $detail['Quantity'] * $detail['UsedPrice'],
                'name' => $detail['MenuDescription'],
                'g5_id' => $detail['OrderDetailID'],
                'item_id' => $detail['ItemID']
            ]);

            $employeeName = $detail['EmployeeName'];
        }

        $amount = OrderItem::where('order_id', $order->id)->get()->sum('amount');
        
        $this->update($order->id, [
            'amount' => $amount,
            'served_by' => $employeeName
        ]);

        if(isset($data['tip_amount']) and ($data['tip_amount'] > 0)){
            try {
                $tip = [
                    "order" => $order->g5_id,
                    "Amount" => $data['tip_amount'],
                    "PaymentTypeID" => 4,
                    "PayAmt" => intval($data['tip_amount']) + intval($amount)
                ];

                $response = $service->tip($tip);
                if($response){
                    $this->update($order->id, [
                        'amount' => intval($data['tip_amount']) + intval($amount)
                    ]);
                }
            } catch(Exception $e){

            }
        }

        if(!empty(auth('user-api')->user()->parent_id)){
            $wallet = Wallet::where('user_id', auth('user-api')->user()->parent_id)->first();
        } else {
            $wallet = Wallet::where('user_id', auth('user-api')->user()->id)->first();
        }

        $wallet->balance -= $amount + ($data['tip_amount'] ?? 0);
        $wallet->save();

        WalletTransaction::create([
            'wallet_id' => $wallet->id,
            'amount' => $amount + ($data['tip_amount'] ?? 0),
            'type' => 'Debit',
            'uuid' => Str::uuid().'-'.time(),
            'is_user_credited' => false,
            'payment_processor' => "G5 POS",
            'external_reference' => $order->g5_id
        ]);

        return $order;
    }

    public function pending_orders($limit=4){
        $data = [
            ['delivery_status', '!=', 'Delivered']
        ];
        $orderBy = [
            ['date_ordered', 'desc']
        ];
        $orders = $this->findBy($data, $orderBy, $limit);
        return $orders;
    }

    public function all_past_orders($limit=15){
        $data = ['delivery_status' => 'Delivered'];
        $orderBy = [
            ['date_ordered', 'desc']
        ];
        $orders = $this->findBy($data, $orderBy, $limit);
        return $orders;
    }

    public function admin_search($order_no=null, $limit=15){
        if(empty($order_no)){
            $orderBy = [
                ['date_ordered', 'desc']
            ];
            $orders = $this->all(
                orderBy: $orderBy,
                limit: $limit
            );
        } else {
            $orders = Order::where('g5_order_number', 'like', '%'.$order_no.'%')->orderBy('date_ordered', 'desc')->paginate($limit);
        }

        return $orders;
    }

    public function summary(){
        $pending_count = [
            ['delivery_status', '!=', 'Delivered']
        ];
        $dining_count = [
            ['delivery_status', '!=', 'Delivered'],
            ['type', 'DiningIn']
        ];
        $delivery_count = [
            ['delivery_status', '!=', 'Delivered'],
            ['type', 'Delivery']
        ];
        $takeaway_count = [
            ['delivery_status', '!=', 'Delivered'],
            ['type', 'TakeAway']
        ];
        $stuff_count = [
            ['delivery_status', '!=', 'Delivered'],
            ['type', 'StuffMeal']
        ];
        $drive_count = [
            ['delivery_status', '!=', 'Delivered'],
            ['type', 'DriveThru']
        ];

        return [
            'total_pendings' => $this->findBy($pending_count, [], null, true),
            'dining_count' => $this->findBy($dining_count, [], null, true),
            'delivery_count' => $this->findBy($delivery_count, [], null, true),
            'takeaway_count' => $this->findBy($takeaway_count, [], null, true),
            'stuff_count' => $this->findBy($stuff_count, [], null, true),
            'drive_count' => $this->findBy($drive_count, [], null, true)
        ];
    }
}