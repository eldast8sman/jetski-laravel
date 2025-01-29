<?php

namespace App\Repositories;

use App\Models\Event;
use App\Repositories\Interfaces\EventRepositoryInterface;
use App\Services\FileManagerService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EventRepository extends AbstractRepository implements EventRepositoryInterface
{
    public $errors;

    public function __construct(Event $event)
    {
        parent::__construct($event);
    }

    public function store(Request $request)
    {
        $all = $request->except(['photo']);
        if($request->has('photo') and !empty($request->photo)){
            $photo = FileManagerService::upload_file($request->file('photo'), env('FILESYSTEM_DISK'));
            if($photo){
                $all['photo'] = $photo->id;
            }
        }
        $uuid = Str::uuid().'-'.time();
        $all['uuid'] = $uuid;

        if(!$event = $this->create($all)){
            $this->errors = "Event creation failed";
            return false;
        }

        return $event;
    }

    public function events($limit = 10)
    {
        $data = [
            ['date', '>=', Carbon::now()]
        ];
        $orderBy = [
            ['date' => 'desc']
        ];
        $events = $this->findBy($data, $orderBy, $limit);
        return $events;
    }

    public function userEvents($user_id, $limit=10){
        $data = [
            ['user_id', '=', $user_id],
            ['date', '>=', Carbon::now()]
        ];
        $orderBy = [
            ['date' => 'desc']
        ];

        $events = $this->findBy($data, $orderBy, $limit);
        return $events;
    }

    public function userPastEvents($user_id, $limit=10){
        $data = [
            ['user_id', '=', $user_id],
            ['date', '<', Carbon::now()]
        ];
        $orderBy = [
            ['date' => 'desc']
        ];

        $events = $this->findBy($data, $orderBy, $limit);
        return $events;
    }

    public function pastEents($limit = 10)
    {
        $data = [
            ['date', '<', Carbon::now()]
        ];
        $orderBy = [
            ['date' => 'desc']
        ];
        $events = $this->findBy($data, $orderBy, $limit);
        return $events;
    }

    public function updateEvent(Request $request, $id)
    {
        $event = $this->findFirstBy(['uuid' => $id]);
        if(empty($event)){
            $this->errors = "No Event found";
            return false;
        }
        if($request->has('photo') and !empty($request->photo)){
            $photo = FileManagerService::upload_file($request->file('photo'), env('FILESYSTEM_DISK'));
            if($photo){
                $all['photo'] = $photo->id;
                $old_photo = $event->photo;
            }
        }
        $event->update($all);
        if(isset($old_photo)){
            FileManagerService::delete($old_photo);
        }

        return $event;
    }

    public function deleteEvent(string $id)
    {
        $event = $this->findFirstBy(['uuid' => $id]);
        if(empty($event)){
            $this->errors = "No Event found";
            return false;
        }

        $this->delete($event);
        if(!empty($event->photo)){
            FileManagerService::delete($event->photo);
        }

        return true;
    }

    public function showEvent(string $id)
    {
        $event = $this->findFirstBy(['uuid' => $id]);
        if(empty($event)){
            $this->errors = "No Event was found";
            return false;
        }

        return $event;
    }
}