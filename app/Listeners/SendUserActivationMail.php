<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Mail\AddUserNotificationMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendUserActivationMail implements ShouldQueue
{
    use InteractsWithQueue;
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
    public function handle(UserRegistered $event): void
    {
        $user = $event->user;
        if(!empty($user->email)){
            $user->name = $user->firstname;
            Mail::to($user)->send(new AddUserNotificationMail($user->name, $user->verification_token, $user->email));
        }
    }
}
