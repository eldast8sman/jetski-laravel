<?php

namespace App\Listeners;

use App\Events\AdminRegistered;
use App\Mail\Admin\AddAdminNotificationMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendAdminActivationMail
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(AdminRegistered $event): void
    {
        $admin = $event->admin;
        if(!empty($admin->email)){
            $admin->name = $admin->firstname;
            Mail::to($admin)->send(new AddAdminNotificationMail($admin->name, $admin->verification_token));
        }
    }
}
