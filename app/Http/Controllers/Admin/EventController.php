<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreEventRequest;
use App\Http\Requests\Admin\UpdateEventRequest;
use App\Http\Resources\Admin\EventResource;
use App\Jobs\SendNotificationJob;
use App\Models\NotificationImage;
use App\Models\User;
use App\Repositories\Interfaces\EventRepositoryInterface;
use Illuminate\Http\Request;

class EventController extends Controller
{
    private $event;

    public function __construct(EventRepositoryInterface $event)
    {
        $this->event = $event;
    }

    public function store(StoreEventRequest $request){
        if(!$event = $this->event->store($request)){
            return $this->failed_response($this->event->errors, 400);
        }

        $image = NotificationImage::find($event->notification_image_id);
        $users = User::whereNotNull('notification_token')->where('notification', true)->get(['notification_token']);
        if(!empty($users)){
            foreach($users as $user){
                SendNotificationJob::dispatch($user->token, $event->title, $event->description, $image->photo);
            }
        }

        return $this->success_response('Event successfully added', $event);
    }

    public function index(){
        $limit = !empty($_GET['limit']) ? (int)$_GET['limit'] : 10;

        $events = $this->event->events($limit);
        return $this->success_response("Events fetched successfully", EventResource::collection($events)->response()->getData(true));
    }

    public function past_events(){
        $limit = !empty($_GET['limit']) ? (int)$_GET['limit'] : 10;

        $events = $this->event->pastEents($limit);
        return $this->success_response("Events fetched successfully", EventResource::collection($events)->response()->getData(true));
    }

    public function update(UpdateEventRequest $request, $uuid){
        if(!$event = $this->event->updateEvent($request, $uuid)){
            return $this->failed_response($this->event->errors, 400);
        }

        $image = NotificationImage::find($event->notification_image_id);
        $users = User::whereNotNull('notification_token')->where('notification', true)->get(['notification_token']);
        if(!empty($users)){
            foreach($users as $user){
                SendNotificationJob::dispatch($user->token, $event->title, $event->description, $image->photo);
            }
        }

        return $this->success_response('Event successfully updated', $event);
    }

    public function destroy($uuid){
        if(!$this->event->deleteEvent($uuid)){
            return $this->failed_response("Failed to delete");
        }

        return $this->success_response("Event successfully deleted");
    }
}
