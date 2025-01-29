<?php

namespace App\Jobs;

use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $token;
    public $title;
    public $body;
    public $image;

    /**
     * Create a new job instance.
     */
    public function __construct($token, $title, $body, $image)
    {
        $this->token = $token;
        $this->title = $title;
        $this->body = $body;
        $this->image = $image;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $notification = new NotificationService();
        $notification->send($this->token, $this->title, $this->body, $this->image, []);
    }
}
