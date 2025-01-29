<?php

namespace App\Repositories;

use App\Models\Announcement;
use App\Repositories\Interfaces\AnnouncementRepositoryInterface;
use App\Services\FileManagerService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AnnouncementRepository extends AbstractRepository implements AnnouncementRepositoryInterface
{
    public $errors;

    public function __construct(Announcement $announcement)
    {
        parent::__construct($announcement);
    }

    public function store(Request $request)
    {
        $all = $request->except(['photo']);
        if($request->has('photo') and !empty($request->photo)){
            $photo = FileManagerService::upload_file($request->file('photo'), env('FILESYSTEM_DISK'));
            if($photo){
                $all['photo'] = $photo->id;
            }
        }
        $uuid = Str::uuid().'-'.time();
        $all['uuid'] = $uuid;

        if(!$announcement = $this->create($all)){
            $this->errors = "Failed to create Announcement";
            return false;
        }

        return $announcement;
    }

    public function index($limit=10)
    {
        $orderBy = [
            ['created_at', 'desc']
        ];
        $announcements = $this->all($orderBy, $limit);

        return $announcements;
    }

    public function show(string $uuid)
    {
        $announcement = $this->findFirstBy(['uuid' => $uuid]);
        if(empty($announcement)){
            $this->errors = "No Announcement was fetched";
            return false;
        }
        return $announcement;
    }
}