<?php

namespace App\Providers;

use App\Models\Admin;
use App\Models\Booking;
use App\Models\Order;
use App\Models\OrderCart;
use App\Models\OrderCartItem;
use App\Observers\AdminObserver;
use App\Observers\BookingObserver;
use App\Observers\OrderCartItemObserver;
use App\Observers\OrderCartObserver;
use App\Observers\OrderObserver;
use Illuminate\Support\ServiceProvider;

class ObserverServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Admin::observe(AdminObserver::class);
        Booking::observe(BookingObserver::class);
        Order::observe(OrderObserver::class);
        OrderCartItem::observe(OrderCartItemObserver::class);
        OrderCart::observe(OrderCartObserver::class);
    }
}
