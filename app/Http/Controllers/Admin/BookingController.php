<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\BookingResource;
use App\Repositories\Interfaces\BookingRepositoryInterface;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    private $booking;

    public function __construct(BookingRepositoryInterface $booking)
    {
        $this->booking = $booking;
    }

    public function index(){
        $limit = !empty($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $bookings = $this->booking->adminIndex($limit);
        return $this->success_response("Bookings fetched successfully", BookingResource::collection($bookings)->response()->getData(true));
    }

    public function pastBookings(){
        $limit = !empty($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $bookings = $this->booking->allPastBookings($limit);
        return $this->success_response("Bookings fetched successfully", BookingResource::collection($bookings)->response()->getData(true));
    }
}
