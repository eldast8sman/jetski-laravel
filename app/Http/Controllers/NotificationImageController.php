<?php

namespace App\Http\Controllers;

use App\Models\NotificationImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class NotificationImageController extends Controller
{
    public function index(){
        $images = Cache::remember('notification_images', 1200, function(){
            return NotificationImage::all();
        });

        return $this->success_response([
            'status' => 'success',
            'message' => 'Notification Images fetched',
            'data' => $images
        ]);
    }
}
