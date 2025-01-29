<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminBankAccountDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'bank_name',
        'bank_code',
        'nip_code',
        'account_number',
        'account_name'
    ];

    public function admin(){
        return $this->belongsTo(Admin::class);
    }
}
