<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAnnouncementRequest;
use App\Http\Resources\Admin\AnnouncementResource;
use App\Jobs\SendNotificationJob;
use App\Models\NotificationImage;
use App\Models\User;
use App\Repositories\Interfaces\AnnouncementRepositoryInterface;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    private $announcement;

    public function __construct(AnnouncementRepositoryInterface $announcement)
    {
        $this->announcement = $announcement;
    }

    public function store(StoreAnnouncementRequest $request){
        if(!$ann = $this->announcement->store($request)){
            return $this->failed_response($this->announcement->errors, 500);
        }

        $image = NotificationImage::find($ann->notification_image_id);
        $users = User::whereNotNull('notification_token')->where('notifications', true)->get(['notification_token']);
        if(!empty($users)){
            foreach($users as $user){
                SendNotificationJob::dispatch($user->token, $ann->type, $ann->information, $image->photo);
            }
        }

        return $this->success_response("Announcement ssuccessfully created", $ann);
    }

    public function index(){
        $limit = !empty($_GET['limit']) ? (int)$_GET['limit'] : 10;

        $announcements = $this->announcement->index($limit);
        return $this->success_response("Announcements fetched successfuly", AnnouncementResource::collection($announcements)->response()->getData(true));
    }

    public function show($uuid){
        $announcement = $this->announcement->show($uuid);
        if(!$announcement){
            return $this->failed_response($this->announcement->errors);
        }

        return $this->success_response("Announcement fetched successfully", new AnnouncementResource($announcement));
    }
}
