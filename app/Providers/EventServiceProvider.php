<?php

namespace App\Providers;

use App\Events\AdminRegistered;
use App\Events\UserRegistered;
use App\Listeners\SendAdminActivationMail;
use App\Listeners\SendUserActivationMail;
use App\Listeners\SparkleUserRegistration;
use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    protected $listen = [
        AdminRegistered::class => [
            SendAdminActivationMail::class,
        ],
        UserRegistered::class => [
            SparkleUserRegistration::class,
            SendUserActivationMail::class
        ]
    ];

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
