<?php

namespace App\Providers;

use App\Models\Admin;
use App\Models\Booking;
use App\Models\FoodMenu;
use App\Models\MembershipType;
use App\Models\Order;
use App\Models\OrderCart;
use App\Models\OrderCartItem;
use App\Models\User;
use App\Observers\AdminObserver;
use App\Observers\BookingObserver;
use App\Observers\FoodMenuObserver;
use App\Observers\MembershipTypeObserver;
use App\Observers\OrderCartItemObserver;
use App\Observers\OrderCartObserver;
use App\Observers\OrderObserver;
use App\Observers\UserObserver;
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
        FoodMenu::observe(FoodMenuObserver::class);
        MembershipType::observe(MembershipTypeObserver::class);
        User::observe(UserObserver::class);
    }
}
