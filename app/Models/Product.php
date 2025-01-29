<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'name',
        'description',
        'amount',
        'available',
        'category',
        'g5_id',
        'screen_id',
        'parent_id',
        'group_id',
        'modifier_id',
        'photo'
    ];

    public function sub_items(){
        return $this->hasMany(SubItem::class);
    }
}
