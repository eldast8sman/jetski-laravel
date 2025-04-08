<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventTicketPricing extends Model
{
    protected $fillable = [
        'uuid',
        'g5_id',
        'audiience',
        'price',
        'name',
        'description'
    ];
}
