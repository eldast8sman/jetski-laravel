<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\OrderItem;
use App\Repositories\OrderRepository;
use App\Services\G5PosService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class SaveOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $order;
    public $user_id;

    /**
     * Create a new job instance.
     */
    public function __construct($order, $user_id)
    {
        $this->order = $order;
        $this->user_id = $user_id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $order = $this->order;
        $repo = new OrderRepository(new Order());
        $data = [
            ['g5_id' => $order['OrderID']],
            ['g5_order_number' => $order['OrderNumber']]
        ];

        if(empty($newOrder = $repo->findByOrFirst($data)) and ($order['TotalPrice'] > 0)){
            preg_match('/[0-9]+/', $order['OrderingTime'], $match);

            $newOrder = $repo->create([
                'user_id' => $this->user_id,
                'uuid' => Str::uuid().'-'.time(),
                'amount' => $order['TotalPrice'],
                'description' => $order['Description'],
                'paid_from' => 'Wallet',
                'delivery_status' => $order['DeliveryStatus'] ?? 'Delivered',
                'g5_id' => $order['OrderID'],
                'type' => $order['OrderMenuID'] ?? 'Delivery',
                'g5_order_number' => $order['OrderNumber'],
                'date_ordered' => Carbon::createFromTimestamp(substr($match[0], 0, -3)),
                'served_by' => $order['EmployeeName']
            ]);
        }

        $g5 = new G5PosService();
        $details = $g5->getOrderDetails(['OrderID' => $newOrder->g5_id]);
        $details = json_decode($details, true);
        
        foreach($details as $detail){
            if(empty(OrderItem::where('g5_id', $detail['OrderDetailID'])->first())){
                OrderItem::create([
                    'uuid' => Str::uuid().'-'.time(),
                    'order_id' => $newOrder->id,
                    'quantity' => $detail['Quantity'],
                    'amount' => $detail['Quantity'] * $detail['UsedPrice'],
                    'name' => $detail['MenuDescription'],
                    'g5_id' => $detail['OrderDetailID'],
                    'item_id' => $detail['ItemID']
                ]);
            }
        }
        $amount = OrderItem::where('order_id', $newOrder->id)->get()->sum('amount');
        $newOrder->update(['amount' => $amount]);
    }
}
