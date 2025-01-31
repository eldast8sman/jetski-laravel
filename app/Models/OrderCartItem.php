<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderCartItem extends Model
{
    protected $fillable = [
        'uuid',
        'user_id',
        'order_cart_id',
        'food_menu_id',
        'add_ons',
        'add_on_price',
        'unit_price',
        'total_unit_price',
        'quantity',
        'total_price',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order_cart()
    {
        return $this->belongsTo(OrderCart::class);
    }

    public function food_menu()
    {
        return $this->belongsTo(FoodMenu::class);
    }
}
