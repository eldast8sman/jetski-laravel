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
        'tickets_pricing'
    ];

    public function photo()
    {
        return $this->belongsTo(FileManager::class, 'photo_id', 'id');
    }

    public function bookings()
    {
        return $this->hasMany(JetskiEventBooking::class, 'jetski_event_id', 'id')->groupBy('user_id');
    }
}
