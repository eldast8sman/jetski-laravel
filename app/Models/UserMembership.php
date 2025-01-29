<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserMembership extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'uuid',
        'membership_id',
        'amount',
        'payment_date',
        'date_joined',
        'expiry_date',
        'membership_notes',
        'active_diver',
        'padi_level',
        'padi_number',
        'company',
        'department',
        'referee1',
        'referee2',
        'referee3',
        'referee4',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function membership(){
        return $this->belongsTo(Product::class, 'membership_id', 'id');
    }
}
