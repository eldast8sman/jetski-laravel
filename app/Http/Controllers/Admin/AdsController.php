<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAdRequest;
use App\Http\Requests\Admin\UpdateAdsRequest;
use App\Http\Requests\Admin\UpdateAdStatusRequest;
use App\Http\Resources\Admin\AdsResource;
use App\Repositories\Interfaces\AdsRepositoryInterface;
use Illuminate\Http\Request;

class AdsController extends Controller
{
    private $ad;

    public function __construct(AdsRepositoryInterface $ad)
    {
        $this->ad = $ad;
    }

    public function store(StoreAdRequest $request){
        if(!$ad = $this->ad->store($request)){
            return $this->failed_response($this->ad->errors, 400);
        }

        return $this->success_response('Advert added successfully', new AdsResource($ad));
    }

    public function index(){
        $limit = !empty($_GET['limit']) ?$_GET['limit'] : 10;

        $ads = $this->ad->index($limit);
        return $this->success_response("Adverts fetched successfuly", AdsResource::collection($ads)->response()->getData(true));
    }

    public function show($uuid){
        $ad = $this->ad->show($uuid);
        if(empty($ad)){
            $this->failed_response("No Advert was fetched", 400);
        }
        return $this->success_response("Advert fetched successfully", new AdsResource($ad));
    }
    

    public function update(UpdateAdsRequest $request, $uuid){
        if(!$ad = $this->ad->edit($uuid, $request)){
            $this->failed_response($this->ad->errors, 400);
        }

        return $this->success_response("Advert updated successfully", new AdsResource($ad));
    }

    public function change_status(UpdateAdStatusRequest $request, $uuid){
        if(!$ad = $this->ad->change_status($uuid, $request->status)){
            $this->failed_response($this->ad->errors, 400);
        }

        return $this->success_response("Advert status changed successfully", new AdsResource($ad));
    }

    public function destroy($uuid){
        if(!$this->ad->destroy($uuid)){
            return $this->failed_response("Failed to delete");
        }

        return $this->success_response("Advert successfully deleted");
    }
}