<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JetskiEventBooking extends Model
{
    protected $fillable = [
        'uuid',
        'user_id',
        'booking_reference',
        'g5_id',
        'g5_order_number',
        'jetski_event_id',
        'tickets',
        'total_quantity',
        'total_amount',
        'status'
    ];

    public function jetskiEvent()
    {
        return $this->belongsTo(JetskiEvent::class, 'jetski_event_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
