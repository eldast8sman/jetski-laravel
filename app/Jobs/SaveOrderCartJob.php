<?php

namespace App\Jobs;

use App\Models\FoodMenu;
use App\Models\OrderCart;
use App\Models\OrderCartItem;
use App\Models\User;
use App\Repositories\FoodMenuRepository;
use App\Repositories\OrderCartItemRepository;
use App\Repositories\OrderCartRepository;
use App\Services\G5PosService;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Str;

class SaveOrderCartJob implements ShouldQueue
{
    use Queueable;

    private $user;
    private $order;
    private $user_id;
    private $order_repo;
    private $item_repo;
    private $menu_repo;

    /**
     * Create a new job instance.
     */
    public function __construct($order, User $user)
    {
        $this->user = $user;
        $this->order = $order;
        $this->user = $user;
        $this->order_repo = new OrderCartRepository(new OrderCart());
        $this->item_repo = new OrderCartItemRepository(new OrderCartItem());
        $this->menu_repo = new FoodMenuRepository(new FoodMenu());
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $order = $this->order;
        $order_repo = $this->order_repo;
        $item_repo = $this->item_repo;
        $menu_repo = $this->menu_repo;

        $criteria = [
            ['g5_id' => $order['OrderID']],
            ['g5_order_number' => $order['OrderNumber']]
        ];

        if(empty($new_order = $order_repo->findByOrFirst($criteria)) and ($order['TotalPrice'] > 0)){
            preg_match('/[0-9]+/', $order['OrderingTime'], $match);
            if($order['OrderMenuID'] == 1){
                $order_type = 'Dine-In';
            } elseif($order['OrderMenuID'] == 2){
                $order_type = 'Delivery';
            } elseif($order['OrderMenuID'] == 3){
                $order_type = 'Pickup';
            } else {
                $order_type = 'Unknown';
            }
            $new_order = $order_repo->create([
                'user_id' => $this->user->id,
                'uuid' => Str::uuid().'-'.time(),
                'g5_id' => $order['OrderID'],
                'g5_order_number' => $order['OrderNumber'],
                'user_name' => $this->user->firstname.' '.$this->user->lastname,
                'order_type' => $order_type,
                'status' => $order['DeliveryStatus'] ?? 'Completed',
                'open' => 0,
                'time_ordered' => Carbon::createFromTimestamp(substr($match[0], 0, -3), 'Africa/Lagos')->format('Y-m-d H:i:s'),
                'item_amount' => $order['TotalPrice'],
                'total_amount' => $order['TotalPrice'],
                'delivery_email' => $this->user->email,
                'delivery_phone' => $this->user->phone
            ]);

            $g5 = new G5PosService();
            $details = $g5->getOrderDetails(['OrderID' => $new_order->g5_id]);
            $details = json_decode($details, true);

            foreach($details as $detail){
                $menu = $menu_repo->findFirstBy(['g5_id' => $detail['ItemID']]);
                $item_repo->create([
                    'uuid' => Str::uuid().'-'.time(),
                    'user_id' => $this->user->id,
                    'order_cart_id' => $new_order->id,
                    'food_menu_id' => $menu->id,
                    'unit_price' => $detail['UsedPrice'],
                    'total_unit_price' => $detail['UsedPrice'],
                    'quantity' => $detail['Quantity'],
                    'total_price' => $detail['Quantity'] * $detail['UsedPrice']
                ]);
            }
        }
    }
}