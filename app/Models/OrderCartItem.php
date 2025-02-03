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

    public function sorted_add_ons($type='user')
    {
        $return = [];
        if(empty($this->add_ons)){
            return null;
        }
        $add_ons = json_decode($this->add_ons, true);
        foreach($add_ons as $add_on){
            $menu = FoodMenu::find($add_on['id']);
            if(!empty($menu)){
                $return[] = [
                    'identifier' => ($type == 'user') ? $menu->slug : $menu->uuid,
                    'name' => $menu->name,
                    'unit_price' => $add_on['unit_price'],
                    'total_price' => $add_on['total_price'],
                ];
            }
        }
        return $return;
    }
}
