<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionTracker extends Model
{
    protected $fillable = ['last_sync'];
}
