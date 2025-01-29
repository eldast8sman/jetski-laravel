<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'uuid',
        'description',
        'date',
        'photo',
        'guest_amount',
        'created_guests',
        'link'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function invites()
    {
        return $this->hasMany(Invite::class);
    }

    public function photo(){
        return $this->belongsTo(FileManager::class, 'photo', 'id');
    }
}
