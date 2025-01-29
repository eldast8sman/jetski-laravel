<?php

namespace App\Http\Controllers;

use App\Http\Resources\AdsResource;
use App\Repositories\Interfaces\AdsRepositoryInterface;
use Illuminate\Http\Request;

class AdsController extends Controller
{
    private $ad;

    public function __construct(AdsRepositoryInterface $ad)
    {
        $this->ad = $ad;
    }

    public function index(){
        $ads = $this->ad->user_index();

        return $this->success_response('Adverts fetched successfully', AdsResource::collection($ads));
    }

    public function click_increment($uuid){
        $this->ad->click_increment($uuid);

        return $this->success_response('Operation successful');
    }
}
