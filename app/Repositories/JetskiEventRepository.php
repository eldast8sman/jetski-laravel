<?php

namespace App\Repositories;

use App\Models\EventTicketPricing;
use App\Models\JetskiEvent;
use App\Repositories\Interfaces\JetskiEventRepositoryInterface;
use App\Services\FileManagerService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class JetskiEventRepository extends AbstractRepository implements JetskiEventRepositoryInterface
{
    public $errors = "";

    public function __construct(JetskiEvent $event)
    {
        parent::__construct($event);
    }

    private function sort_date(array $data){
        $date_times = json_decode($data['date_time'], true);

        $dates = [];

        $start_date = "";
        $end_date = "";

        foreach($date_times as $date_time){
            $dates[] = $date_time['date'];
        }

        sort($dates);
        $start_date = array_shift($dates);
        $end_date = !empty($dates) ? array_pop($dates) : $start_date;

        $data['date_from'] = $start_date;
        $data['date_to'] = $end_date;
        $data['audience'] = json_encode($data['audience']);

        return $data;
    }

    private function sort_ticketing(array $pricings){
        $tickets = [];
        foreach($pricings as $pricing){
            $ticket = EventTicketPricing::where('uuid', $pricing['uuid'])->first();
            if(!empty($ticket)){
                $tickets[] = [
                    'audience' =>  $pricing['audience'],
                    'id' => $ticket->id,
                    'total_quantity' => $pricing['total_quantity'],
                    'available_quantity' => $pricing['available_quantity']
                ];
            }
        }

        return json_encode($tickets);
    }

    public function store(Request $request)
    {
        $all = $request->except(['photo', 'tickets_pricing', 'location_type']);       

        $all['tickets_pricing'] = !empty($request->tickets_pricing) ? $this->sort_ticketing(json_decode($request->tickets_pricing, true)) : "";

        if(!empty($request->photo)){
            if($upload = FileManagerService::upload_file($request->photo, env('FILESYSTEM_DISK'))){
                $all['photo_id'] = $upload->id;
            }
        }

        $all['uuid'] = Str::uuid().' '.time();
        $all['details'] = $all['event_title'].' '.$all['description'];
        $all['location_type'] = 'Physical';
        $event = $this->create($this->sort_date($all));
        return $event;
    }

    public function index($search = "", $from = "", $to = "", $sort="desc", $limit = 10, $user=false)
    {
        $data =  [];
        if($user){
            $data[] = ['status','=', 1];
        }
        if(!empty($search)){
            $data[] = ['details', 'like', '%'.$search.'%'];
        }
        if(!empty($from)){
            $data[] = ['date_from', '>=', $from];
        }
        if(!empty($to)){
            $data[] = ['date_to', '<=', $to];
        }

        $orderBy = [
            ['date_from', $sort],
            ['date_to', $sort]
        ];

        $events = $this->findBy($data, $orderBy, $limit);
        return $events; 
    }

    public function upcoming_events($search="", $from="", $to="", $sort="asc", $limit=10, $user=false)
    {
        $today = Carbon::now('Africa/Lagos')->toDateString();
        $data = [
            ['date_to', '>=', $today]
        ];
        if($user){
            $data[] = ['status','=', 1];
        }
        if(!empty($search)){
            $data[] = ['details', 'like', '%'.$search.'%'];
        }
        if(!empty($from)){
            $data[] = ['date_from', '>=', $from];
        }
        if(!empty($to)){
            $data[] = ['date_to', '<=', $to];
        }

        $orderBy = [
            ['date_from', $sort],
            ['date_to', $sort]
        ];

        $events = $this->findBy($data, $orderBy, $limit);
        return $events;
    }

    public function show($uuid){
        $event = $this->findByUuid($uuid);
        return $event;
    }

    public function update_event(Request $request, string $uuid){
        $event = $this->findByUuid($uuid);
        if(empty($event)){
            $this->errors = "No Event found";
            return false;
        }

        $all = $request->except(['photo', 'tickets_pricing']);

        $all['tickets_pricing'] = !empty($request->tickets_pricing) ? $this->sort_ticketing(json_decode($request->tickets_pricing, true)) : "";

        if(!empty($request->photo)){
            if($upload = FileManagerService::upload_file($request->photo, env('FILESYSTEM_DISK'))){
                $all['photo_id'] = $upload->id;

                $old_photo = $event->photo_id;
            }
        }

        $all['details'] = $all['event_title'].' '.$all['description'];
        $updated = $this->update($event->id, $this->sort_date($all));
        if(!$updated){
            $this->errors = $this->error_msg;
            return false;
        }

        if(isset($old_photo)){
            FileManagerService::delete($old_photo);
        }

        return $updated;
    }
    
    public function delete_event(string $uuid)
    {
        $event = $this->findByUuid($uuid);
        if(empty($event)){
            $this->errors = "No Event found";
            return false;
        }

        $deleted = $this->delete($event);
        if(!$deleted){
            $this->errors = $this->error_msg;
            return false;
        }

        if(!empty($event->photo)){
            FileManagerService::delete($event->photo_id);
        }

        return true;
    }

    public function event_tickets($search = ""){
        parent::__construct(new EventTicketPricing());
        $orderBy = [
            ['name', 'asc']
        ];
        if(empty($search)){
            $tickets = $this->all($orderBy);
        } else {
            $tickets = $this->findBy([['name', 'like', '%'.$search.'%']], $orderBy);
        }

        return $tickets;
    }
}