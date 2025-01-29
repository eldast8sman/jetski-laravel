<?php

namespace App\Repositories;

use App\Models\Booking;
use App\Models\Invite;
use App\Repositories\Interfaces\BookingRepositoryInterface;
use App\Services\FileManagerService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class BookingRepository extends AbstractRepository implements BookingRepositoryInterface
{
    public $errors;

    public function __construct(Booking $booking)
    {
        parent::__construct($booking);
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
        $all['user_id'] = auth('user-api')->user()->id;
        $uuid = Str::uuid().'-'.time();
        $all['uuid'] = $uuid;
        $all['link'] = env('APP_URL').'/booking-invite/'.urlencode($uuid);
        if(!$booking = $this->create($all)){
            $this->errors = "Booking creation failed";
            return false;
        }

        return $booking;
    }

    public function index($limit=10)
    {
        $data = [
            ['user_id', auth()->user()->id],
            ['date', '>=', Carbon::now()]
        ];
        $orderBy = [
            ['date', 'desc']
        ];
        
        $bookings = $this->findBy($data, $orderBy, $limit);
        return $bookings;
    }

    public function adminIndex($limit=4)
    {
        $data = [
            ['date', '>=', Carbon::now()]
        ];
        $orderBy = [
            ['date', 'desc']
        ];

        $bookings = $this->findBy($data, $orderBy, $limit);
        return $bookings;
    }

    public function pastBookings($limit=10){
        $data = [
            ['user_id', auth()->user()->id],
            ['date', '<=', Carbon::now()]
        ];
        $orderBy = [
            ['date', 'desc']
        ];
        $bookings = $this->findBy($data, $orderBy, $limit);
        return $bookings;
    }

    public function allPastBookings($limit = 10)
    {
        $data = [
            ['date', '<=', Carbon::now()]
        ];
        $orderBy = [
            ['date', 'desc']
        ];
        $bookings = $this->findBy($data, $orderBy, $limit);
        return $bookings;
    }

    public function showBooking(string $id)
    {
        $booking = $this->findFirstBy(['uuid' => $id]);
        if(empty($booking) or ($booking->user_id != auth('user-api')->user()->id)){
            $this->errors = "No Booking found";
            return false;
        }
        return $booking;
    }

    public function updateBooking(Request $request, string $id)
    {
        $booking = $this->findFirstBy(['uuid' => $id]);
        if(empty($booking) or ($booking->user_id != auth('user-api')->user()->id)){
            $this->errors = "No Booking found";
            return false;
        }
        $all = $request->except(['photo']);
        if($request->has('photo') and !empty($request->photo)){
            $photo = FileManagerService::upload_file($request->file('photo'), env('FILESYSTEM_DISK'));
            if($photo){
                $all['photo'] = $photo->id;
                $old_photo = $booking->photo;
            }
        }
        $booking->update($all);
        if(isset($old_photo)){
            FileManagerService::delete($old_photo);
        }

        return $booking;
    }

    public function deleteBooking(string $id)
    {
        $booking = $this->findFirstBy(['uuid' => $id]);
        if(empty($booking) or ($booking->user_id != auth('user-api')->user()->id)){
            $this->errors = "No Booking found";
            return false;
        }

        $this->delete($booking);
        if(!empty($booking->photo)){
            FileManagerService::delete($booking->photo);
        }

        return true;
    }

    public function booking_summary()
    {
        return [
            'total_bookings' => $this->all([], null, true),
            'pending_bookings' => $this->findBy([
                ['date', '>=', Carbon::now()]
            ], [], null, true),
            'past_bookings' => $this->findBy([
                ['date', '<', Carbon::now()]
            ], [], null, true)
        ];
    }
}