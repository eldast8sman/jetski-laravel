<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MembershipInformation extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'user_id',
        'title',
        'make',
        'model',
        'hin_number',
        'year',
        'loa',
        'beam',
        'draft',
        'nwa',
        'nwa_expiry',
        'mmsi',
        'call_sign'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
