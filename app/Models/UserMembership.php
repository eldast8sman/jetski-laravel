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
        $memberships = [];
        if(!empty($this->membership_id)){
            $mem_ids = explode(',', $this->membership_id);
            foreach($mem_ids as $mem_id){
                $type = MembershipType::where('uuid', $mem_id)->first(['uuid', 'slug', 'name']);
                $memberships[] = $type;
            }
        }

        return !empty($memberships) ? $memberships : null;
    }
}
