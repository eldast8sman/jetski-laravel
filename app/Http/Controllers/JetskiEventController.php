<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookEventTicketRequest;
use App\Http\Resources\EventBookingResource;
use App\Http\Resources\JetskiEventResource;
use App\Repositories\Interfaces\EventBookingRepositoryInterface;
use App\Repositories\Interfaces\JetskiEventRepositoryInterface;
use Illuminate\Http\Request;

class JetskiEventController extends Controller
{
    public $event;
    public $booking;

    public function __construct(JetskiEventRepositoryInterface $event, EventBookingRepositoryInterface $booking)
    {
        $this->event = $event;
        $this->booking = $booking;
    }

    public function index(Request $request){
        $search = !empty($request->has('search')) ? $request->search : "";
        $from = !empty($request->has('from')) ? $request->from : "";
        $to = !empty($request->has('to')) ? $request->to : "";
        $sort = !empty($request->has('sort')) ? $request->sort : "desc";
        $limit = !empty($request->has('limit')) ? $request->limit : 10;

        $events = $this->event->index($search, $from, $to, $sort, $limit, true);
        return $this->success_response("Events fetched successfully", JetskiEventResource::collection($events)->response()->getData(true));
    }

    public function upcoming_events(Request $request){
        $search = !empty($request->has('search')) ? $request->search : "";
        $from = !empty($request->has('from')) ? $request->from : "";
        $to = !empty($request->has('to')) ? $request->to : "";
        $sort = !empty($request->has('sort')) ? $request->sort : "desc";
        $limit = !empty($request->has('limit')) ? $request->limit : 10;

        $events = $this->event->upcoming_events($search, $from, $to, $sort, $limit, true);
        return $this->success_response("Events fetched successfully", JetskiEventResource::collection($events)->response()->getData(true));
    }

    public function show($uuid){
        $event = $this->event->show($uuid);
        if(empty($event)){
            return $this->failed_response($this->event->errors, 404);
        }

        return $this->success_response("Event fetched successfully", new JetskiEventResource($event));
    }

    public function book_tickets(BookEventTicketRequest $request){
        $event = $this->event->show($request->event_id);
        if(empty($event)){
            return $this->failed_response($this->event->errors, 404);
        }
        $all = $request->all();
        $booking = $this->booking->store($all['tickets'], $event->id, auth('user-api')->user()->id);
        if(!$booking){
            return $this->failed_response($this->booking->errors);
        }

        return $this->success_response("Event Booked successfully", new EventBookingResource($booking));
    }

    public function booking_index(Request $request){
        $limit = $request->has('limit') ? $request->limit : 10;

        $bookings = $this->booking->index($limit, auth('user-api')->user()->id);
        return $this->success_response("Event Bookings fetched successfully", EventBookingResource::collection($bookings)->response()->getData(true));
    }
}
