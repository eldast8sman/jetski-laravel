<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDeliveryAddress extends Model
{
    protected $fillable = [
        'user_id',
        'uuid',
        'address',
    ];

    protected $casts = [
        'user_id' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
