<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SparkleWebhook extends Model
{
    use HasFactory;

    protected $fillable = [
        'data',
        'user_id',
        'user_name',
        'amount',
        'g5_response'
    ];
}
