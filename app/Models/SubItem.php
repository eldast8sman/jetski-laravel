<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'product_id',
        'name',
        'description',
        'photo'
    ];

    public function product(){
        return $this->belongsTo(Product::class);
    }
}
