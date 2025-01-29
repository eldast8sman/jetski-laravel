<?php

namespace App\Repositories;

use App\Models\Booking;
use App\Models\Invite;
use App\Repositories\Interfaces\InviteRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InviteRepository extends AbstractRepository implements InviteRepositoryInterface
{
    public $errors;

    public function __construct(Invite $invite)
    {
        parent::__construct($invite);
    }

    public function booking(string $uuid)
    {
        $booking = Booking::where('uuid', $uuid)->first();
        if(empty($booking)){
            $this->errors = "Wrong Invite Link";
        }

        return $booking;
    }

    public function store(Request $request, string $uuid)
    {
        if(empty($booking = Booking::where('uuid', $uuid)->first())){
            $this->errors = "Wrong Link";
            return false;
        }
        if($booking->created_guests >= $booking->guest_amount){
            $this->errors = "Invite Limit reached";
            return false;
        }

        $all = $request->all();
        $all['booking_id'] = $booking->id;
        $all['uuid'] = Str::uuid().'-'.time();
        if(!empty($this->findFirstBy(['email' => $all['email'], 'booking_id' => $all['booking_id']]))){
            $this->errors = "You have already accepted the invite to this Event before";
            return false;
        }

        $invite = $this->create($all);
        $booking->created_guests += 1;
        $booking->save();

        return $invite;
    }
}