<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, HasSlug;

    protected $fillable = [
        'uuid',
        'firstname',
        'lastname',
        'username',
        'phone',
        'email',
        'other_emails',
        'password',
        'private_phone',
        'dob',
        'gender',
        'marital_status',
        'address',
        'nationality',
        'religion',
        'membership_id',
        'membership_type_id',
        'exp_date',
        'email_verified',
        'verification_token',
        'verification_token_expiry',
        'token',
        'token_expiry',
        'photo',
        'g5_id',
        'sparkle_id',
        'external_sparkle_reference',
        'account_number',
        'last_synced',
        'next_order_sync',
        'notification_token',
        'relationship',
        'parent_id',
        'notifications',
        'can_use',
        'last_login',
        'prev_login'
    ];

    protected $hidden = [
        'verification_token',
        'verification_token_expiry',
        'token',
        'token_expiry',
        'password'
    ];

    protected function casts(): array
    {
        return [
            'verification_token_expiry' => 'datetime',
            'token_expiry' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function scopeWhereParent($query){
        return $query->whereNull('parent_id')->orWhere('parent_id', 0)->orWhere('parent_id', '');
    }

    public function membership_information(){
        return $this->hasOne(UserMembership::class);
    }

    public function relations(){
        return $this->where('parent_id', $this->id)->get(['uuid', 'relationship', 'firstname', 'lastname', 'phone', 'email', 'dob', 'gender', 'marital_status', 'address', 'photo']);
    }

    public function watercraft(){
        return $this->hasOne(MembershipInformation::class);
    }

    public function employment_detail(){
        return $this->hasOne(EmploymentDetail::class);
    }  

    public function wallet(){
        return $this->hasOne(Wallet::class);
    }

    public function delivery_address(){
        return $this->hasMany(UserDeliveryAddress::class);
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(['firstname', 'lastname'])
            ->saveSlugsTo('username')
            ->usingSeparator('_');
    }
}
