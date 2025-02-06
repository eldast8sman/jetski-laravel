<?php

namespace App\Observers;

use App\Models\OrderCart;
use App\Models\OrderCartItem;
use Illuminate\Support\Str;

class OrderCartItemObserver
{
    /**
     * Handle the OrderCartItem "created" event.
     */
    public function created(OrderCartItem $item): void
    {
        if(!empty($item->order_cart_id)){
            $order = OrderCart::find($item->order_cart_id);
            $this->update_order($order);
        } else {
            $user = $item->user;
            $order = OrderCart::where('user_id', $user->id)->where('open', 1)->first();
            if(empty($order)){
                $order = OrderCart::create([
                    'uuid' => Str::uuid().'-'.time(),
                    'user_id' => $user->id,
                    'user_name' => $user->firstname.' '.$user->lastname
                ]);
            }
            $item->order_cart_id = $order->id;
            $item->save();
    
            $this->update_order($order);
        }
    }

    private function update_order(OrderCart $order): void
    {
        $items = $order->order_cart_items();
        if(empty($items->count())){
            $order->delete();
            return;
        }
        $items = $items->get();
        $order->item_amount = $items->sum('total_price');
        $order->total_quantity = $items->sum('quantity');
        $order->total_amount = $order->item_amount + $order->delivery_amount + $order->service_charge_amount + $order->tip_amount;
        $order->save();
    }

    /**
     * Handle the OrderCartItem "updated" event.
     */
    public function updated(OrderCartItem $item): void
    {
        $order = $item->order_cart;
        $this->update_order($order);
    }

    /**
     * Handle the OrderCartItem "deleted" event.
     */
    public function deleted(OrderCartItem $item): void
    {
        $order = $item->order_cart;
        $this->update_order($order);
    }

    /**
     * Handle the OrderCartItem "restored" event.
     */
    public function restored(OrderCartItem $item): void
    {
        //
    }

    /**
     * Handle the OrderCartItem "force deleted" event.
     */
    public function forceDeleted(OrderCartItem $item): void
    {
        //
    }
}
