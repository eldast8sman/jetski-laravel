<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuestPass extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'event_id',
        'firstname',
        'lastname',
        'email',
        'phone',
        'date',
        'duration'
    ];

    public function event(){
        return $this->belongsTo(Event::class);
    }
}
