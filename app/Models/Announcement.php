<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'type',
        'information',
        'notification_type',
        'notification_image_id',
        'photo'
    ];

    public function notification_image(){
        return $this->belongsTo(NotificationImage::class);
    }

    public function photo(){
        return $this->belongsTo(FileManager::class, 'photo', 'id');
    }
}
