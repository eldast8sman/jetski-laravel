<?php

namespace App\Jobs;

use App\Models\Admin;
use App\Models\Booking;
use App\Models\Notification;
use App\Models\Order;
use App\Models\User;
use App\Repositories\AdminRepository;
use App\Repositories\NotificationRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SaveAdminNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $repo;
    private $type;
    private $type_id;
    private $admins;

    /**
     * Create a new job instance.
     */
    public function __construct($type, $type_id)
    {
        $this->repo = new NotificationRepository(new Notification(), 'admin-api');
        $this->type = $type;
        $this->type_id = $type_id;
        $this->admins = new AdminRepository(new Admin());
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if($this->type == 'booking'){
            $model = Booking::find($this->type_id);
            $user = User::find($model->user_id);

            $body = $user->firstname.' '.$user->lastname.' just made a Booking titled '.$model->title;
        } elseif($this->type == 'order'){
            $model = Order::find($this->type_id);
            $user = User::find($model->user_id);

            $body = $user->firstname.' '.$user->lastname.' just made an order worth of '.$model->amount;
        }

        $admins = $this->admins->all_admins();
        foreach($admins as $admin){
            $this->repo->store([
                'type_id' => $admin->id,
                'body' => $body,
                'photo' => $user->photo,
                'page' => $this->type,
                'identifier' => $this->type_id
            ]);
        }
    }
}
