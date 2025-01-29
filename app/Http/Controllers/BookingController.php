<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Http\Resources\BookingResource;
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
        $limit = !empty($_GET['limit']) ? $_GET['limit'] : 10;
        $bookings = $this->booking->index($limit);
        return $this->success_response("Bookings fetched successfully", BookingResource::collection($bookings)->response()->getData(true));
    }

    public function pastBookings(){
        $limit = !empty($_GET['limit']) ? $_GET['limit'] : 19;
        $bookings = $this->booking->pastBookings($limit);
        return $this->success_response("Past Bookings fetched successfully", BookingResource::collection($bookings)->response()->getData(true));
    }

    public function show($uuid){
        $booking = $this->booking->showBooking($uuid);
        if(empty($booking)){
            return $this->failed_response($this->booking->errors, 404);
        }
        return $this->success_response("Booking fetched successfully", new BookingResource($booking));
    }

    public function store(StoreBookingRequest $request){
        if(!$booking = $this->booking->store($request)){
            return $this->failed_response("Booking Creation failed", 500);
        }
        return $this->success_response("Booking created successfully", new BookingResource($booking));
    }

    public function update(StoreBookingRequest $request, $uuid){
        if(!$booking = $this->booking->updateBooking($request, $uuid)){
            return $this->failed_response($this->booking->errors, 500);
        }
        return $this->success_response("Booking updated successfully", new BookingResource($booking));
    }

    public function destroy($uuid){
        if(!$this->booking->deleteBooking($uuid)){
            return $this->failed_response($this->booking->errors, 404);
        }

        return $this->success_response("Booking deleted successfully");
    }
}
