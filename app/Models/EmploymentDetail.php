<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmploymentDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'uuid',
        'employer',
        'position',
        'industry',
        'address',
        'email',
        'phone',
        'pa_name',
        'pa_email',
        'pa_phone'
    ];
}
