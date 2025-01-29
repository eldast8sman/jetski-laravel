<?php

namespace App\Providers;

use App\Models\Admin;
use App\Models\Booking;
use App\Models\Order;
use App\Models\User;
use App\Observers\AdminObserver;
use App\Observers\BookingObserver;
use App\Observers\OrderObserver;
use App\Observers\UserObsever;
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
        // User::observe(UserObsever::class);
        Booking::observe(BookingObserver::class);
        Order::observe(OrderObserver::class);
    }
}
