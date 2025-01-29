<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FoodMenuPhoto extends Model
{
    protected $fillable = [
        'uuid',
        'food_menu_id',
        'file_manager_id'
    ];

    public function file_manager(){
        return $this->belongsTo(FileManager::class);
    }
}
