<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Admin extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'uuid',
        'firstname',
        'lastname',
        'email',
        'password',
        'phone',
        'photo',
        'role',
        'verification_token',
        'verification_token_expiry',
        'token',
        'token_expiry',
        'activated',
        'status',
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

    public function account(){
        return $this->hasOne(AdminBankAccountDetail::class);
    }
}
