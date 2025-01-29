<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class NotificationService
{
    protected $firebase;

    public function __construct()
    {
        $this->firebase = (new Factory)->withServiceAccount(Storage::disk('public')->get('/fcm/jetski.json'))
                            ->createMessaging();
    }

    public function send($token, $title, $body, $image, $data=[])
    {
        $message = CloudMessage::withTarget('token', $token)
                    ->withNotification(Notification::create($title, $body)->withImageUrl($image))
                    ->withData($data)
                    ->withHighestPossiblePriority();
        
        $this->firebase->send($message);
    }
}