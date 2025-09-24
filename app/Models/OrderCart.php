<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderCart extends Model
{
    protected $fillable = [
        'uuid',
        'order_no',
        'g5_id',
        'g5_order_number',
        'user_id',
        'user_name',
        'order_type',
        'delivery_address',
        'longitude',
        'latitude',
        'delivery_phone',
        'delivery_email',
        'status',
        'delivery_fee_id',
        'delivery_amount',
        'service_charge',
        'service_charge_amount',
        'tip_amount',
        'item_amount',
        'total_amount',
        'total_quantity',
        'open',
        'online_order',
        'order_by_type',
        'order_by_id',
        'time_ordered'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order_cart_items()
    {
        return $this->hasMany(OrderCartItem::class);
    }

    public function order_trackers()
    {
        return $this->hasMany(OrderTracker::class);
    }
}
