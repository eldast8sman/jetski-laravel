<?php

namespace App\Http\Controllers;

use App\Http\Resources\EventResource;
use App\Repositories\Interfaces\EventRepositoryInterface;
use Illuminate\Http\Request;

class EventController extends Controller
{
    private $event;

    public function __construct(EventRepositoryInterface $event)
    {
        $this->event = $event;
    }

    public function index(){
        $limit = !empty($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $events = $this->event->userEvents(auth()->id, $limit);

        return $this->success_response("Events fetched successfully", EventResource::collection($events)->response()->getData());
    }

    public function pastEvents(){
        $limit = !empty($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $events = $this->event->userPastEvents(auth()->id, $limit);

        return $this->success_response("Events fetched successfully", EventResource::collection($events)->response()->getData());
    }
}
