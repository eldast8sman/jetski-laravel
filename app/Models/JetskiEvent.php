<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JetskiEvent extends Model
{
    protected $fillable = [
        'uuid',
        'event_title',
        'description',
        'details',
        'audience',
        'date_time',
        'date_from',
        'date_to',
        'location_type',
        'location',
        'longitude',
        'latitude',
        'photo_id',
        'tickets_pricing',
        'status'
    ];

    public function photo()
    {
        return $this->belongsTo(FileManager::class, 'photo_id', 'id');
    }

    public function bookings()
    {
        return $this->hasMany(JetskiEventBooking::class, 'jetski_event_id', 'id');
    }

    public function tickets(){
        $tickets = [];
        foreach(json_decode($this->tickets_pricing, true) as $ticket){
            $pricing = EventTicketPricing::find($ticket['id']);
            $tickets[] = [
                'uuid' => $pricing->uuid,
                'name' => $pricing->name,
                'description' => $pricing->description,
                'price' => $pricing->price,
                'audience' => $ticket['audience'],
                'total_quantity' => $ticket['total_quantity'],
                'available_quantity' => $ticket['available_quantity']
            ];
        }
        return $tickets;
    }
}
