<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'user_id',
        'amount',
        'description',
        'address',
        'type',
        'category',
        'paid_from',
        'delivery_status',
        'payment_status',
        'date_ordered',
        'g5_id',
        'g5_order_number',
        'served_by'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function order_item(){
        return $this->hasMany(OrderItem::class);
    }
}
