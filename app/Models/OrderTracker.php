<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderTracker extends Model
{
    protected $fillable = [
        'order_cart_id',
        'status',
        'agent_type',
        'agent_id'
    ];
}
