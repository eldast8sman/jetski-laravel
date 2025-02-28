<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JetskiEventBooking extends Model
{
    protected $fillable = [
        'uuid',
        'event_ticket_pricing_id',
        'jetski_event_id',
        'user_id',
        'unit_price',
        'quantity',
        'total_price'
    ];

    public function eventTicketPricing()
    {
        return $this->belongsTo(EventTicketPricing::class, 'event_ticket_pricing_id', 'id');
    }

    public function jetskiEvent()
    {
        return $this->belongsTo(JetskiEvent::class, 'jetski_event_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
