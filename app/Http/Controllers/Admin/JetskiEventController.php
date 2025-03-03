<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreEventRequest;
use App\Http\Requests\Admin\StoreJetskiEventRequest;
use App\Http\Requests\Admin\UpdateJetskiEventRequest;
use App\Http\Resources\Admin\JetskiEventResource;
use App\Jobs\StoreEventTicketPricingJob;
use App\Repositories\Interfaces\JetskiEventRepositoryInterface;
use App\Services\G5PosService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class JetskiEventController extends Controller
{
    protected $event;

    public function __construct(JetskiEventRepositoryInterface $event)
    {
        $this->event = $event;
    }

    public function refresh_ticketing(){
        try {
            $service = new G5PosService();

            $tickets = $service->getMenu([
                'ScreenID' => 53,
                'Type' => 3
            ]);

            dispatch(new StoreEventTicketPricingJob($tickets));

            return $this->success_response('Ticketing refreshed successfully');
        } catch(Exception $e){
            Log::error('Refresh Ticketing: '.$e->getMessage());
            return $this->failed_response('Refresh failed. Check Logs for details');
        }
    }

    public function store(StoreJetskiEventRequest $request){
        if(!$event = $this->event->store($request)){
            return $this->failed_response($this->event->errors, 500);
        }

        return $this->success_response("Event created successfully", new JetskiEventResource($event));
    }

    public function index(Request $request){
        $search = !empty($request->has('search')) ? $request->search : "";
        $from = !empty($request->has('from')) ? $request->from : "";
        $to = !empty($request->has('to')) ? $request->to : "";
        $sort = !empty($request->has('sort')) ? $request->sort : "desc";
        $limit = !empty($request->has('limit')) ? $request->limit : 10;

        $events = $this->event->index($search, $from, $to, $sort, $limit);
        return $this->success_response("Events fetched successfully", JetskiEventResource::collection($events)->response()->getData(true));
    }

    public function upcoming_events(Request $request){
        $search = !empty($request->has('search')) ? $request->search : "";
        $from = !empty($request->has('from')) ? $request->from : "";
        $to = !empty($request->has('to')) ? $request->to : "";
        $sort = !empty($request->has('sort')) ? $request->sort : "asc";
        $limit = !empty($request->has('limit')) ? $request->limit : 10;

        $events = $this->event->upcoming_events($search, $from, $to, $sort, $limit);
        return $this->success_response("Events fetched successfully", JetskiEventResource::collection($events)->response()->getData(true));
    }

    public function show($uuid){
        $event = $this->event->show($uuid);
        if(empty($event)){
            return $this->failed_response($this->event->errors, 404);
        }

        return $this->success_response("Event fetched successfully", new JetskiEventResource($event));
    }

    public function update(UpdateJetskiEventRequest $request, $uuid){
        if(!$event = $this->event->update_event($request, $uuid)){
            return $this->failed_response($this->event->errors, 500);
        }

        return $this->success_response("Event updated successfully", new JetskiEventResource($event));
    }

    public function destroy($uuid){
        if(!$this->event->delete_event($uuid)){
            return $this->failed_response($this->event->errors, 404);
        }

        return $this->success_response("Event deleted successfully");
    }
}
