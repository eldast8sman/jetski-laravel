<?php

namespace App\Repositories;

use App\Models\Advert;
use App\Repositories\Interfaces\AdsRepositoryInterface;
use App\Services\FileManagerService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdsRepository extends AbstractRepository implements AdsRepositoryInterface
{
    public $errors;

    public function __construct(Advert $advert)
    {
        parent::__construct($advert);
    }   

    public function store(Request $request)
    {
        $all = $request->except(['image_banner']);
        $all['status'] = 1;
        if($request->has('image_banner') and !empty($request->image_banner)){
            $photo = FileManagerService::upload_file($request->file('image_banner'), env('FILESYSTEM_DISK'));
            if($photo){
                $all['image_banner'] = $photo->id;
            }
        }
        $all['uuid'] = Str::uuid().'-'.time();
        if(!$ad = $this->create($all)){
            $this->errors = "Advert Upload Failed";
        }
        $today = Carbon::now()->format('Y-m-d');
        if($ad->campaign_start > $today){
            $ad->status = 'draft';
        } else if($ad->campaign_end < $today){
            $ad->status = 'completed';
        } else {
            $ad->status = 'active';
        }
        $ad->save();
        return $ad;
    }

    public function index($limit = 10)
    {
        $order = [
            ['created_at', 'desc']
        ];
        $ads = $this->all($order, $limit);
        return $ads;
    }

    public function user_index()
    {
        $today = Carbon::now()->format('Y-m-d');
        $criteria = [
            ['campaign_start', '<=', $today],
            ['campaign_end', '>=', $today],
            ['status', '=', 'active']
        ];

        $ads = $this->findBy($criteria);
        return $ads;
    }

    public function show(string $id)
    {
        $ad = $this->findFirstBy(['uuid' => $id]);
        if(empty($ad)){
            $this->errors = "No Advert was fetched";
            return false;
        }
        return $ad;
    }

    public function edit($id, $request)
    {
        $ad = $this->findFirstBy(['uuid' => $id]);
        if(empty($ad)){
            $this->errors = "No Advert was fetched";
            return false;
        }
        $all = $request->except(['image_banner']);
        if($request->has('image_banner') and !empty($request->image_banner)){
            $photo = FileManagerService::upload_file($request->file('image_banner'), env('FILESYSTEM_DISK'));
            if($photo){
                $all['image_banner'] = $photo->id;
                $old_photo = $ad->image_banner;
            }
        }
        $ad->update($all);
        if(isset($old_photo)){
            FileManagerService::delete($old_photo);
        }

        return $ad;
    }
    
    public function change_status(string $id, $status='pause')
    {
        if(empty($ad = $this->findFirstBy(['uuid' => $id]))){
            $this->errors = "No Advert was fetched";
            return false;
        }
        $ad->status = $status;
        $ad->save();

        return $ad;
    }

    public function click_increment(string $uuid)
    {
        if(empty($ad = $this->findFirstBy(['uuid' => $uuid]))){
            return false;
        }
        $ad->clicks += 1;
        $ad->save();

        return true;
    }

    public function destroy($id)
    {
        $ad = $this->findFirstBy(['uuid' => $id]);
        if(empty($ad)){
            $this->errors = "No Advert was fetched";
            return false;
        }

        $this->delete($ad);
        if(!empty($ad->image_banner)){
            FileManagerService::delete($ad->image_banner);
        }

        return true;
    }
}