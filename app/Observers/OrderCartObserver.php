<?php

namespace App\Observers;

use App\Models\OrderCart;

class OrderCartObserver
{
    /**
     * Handle the OrderCart "created" event.
     */
    public function created(OrderCart $order): void
    {
        $order->order_no = 'ORD-'.time().'-'.rand(1000, 9999);
        $order->save();
    }

    /**
     * Handle the OrderCart "updated" event.
     */
    public function updated(OrderCart $orderCart): void
    {
        //
    }

    /**
     * Handle the OrderCart "deleted" event.
     */
    public function deleted(OrderCart $orderCart): void
    {
        //
    }

    /**
     * Handle the OrderCart "restored" event.
     */
    public function restored(OrderCart $orderCart): void
    {
        //
    }

    /**
     * Handle the OrderCart "force deleted" event.
     */
    public function forceDeleted(OrderCart $orderCart): void
    {
        //
    }
}
