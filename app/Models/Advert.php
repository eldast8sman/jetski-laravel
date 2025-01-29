<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Advert extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'campaign_name',
        'type',
        'description',
        'image_banner',
        'campaign_start',
        'campaign_end',
        'ads_link',
        'status',
        'clicks',
        'impressions',
        'conversions'
    ];

    public function banner(){
        return $this->belongsTo(FileManager::class, 'image_banner', 'id');
    }
}
