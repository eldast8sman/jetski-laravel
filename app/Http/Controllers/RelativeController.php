<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\UserActivationRequest;
use App\Http\Requests\StoreRelativeRequest;
use App\Http\Requests\UpdateRelativeRequest;
use App\Http\Resources\RelativeResource;
use App\Http\Resources\RelativesResource;
use App\Repositories\Interfaces\UserRelativeRepositoryInterface;
use Illuminate\Http\Request;

class RelativeController extends Controller
{
    protected $relative;

    public function __construct(UserRelativeRepositoryInterface $relative)
    {
        $this->relative = $relative;
    }

    public function index(){
        if(!$relatives = $this->relative->getRelatives()){
            return $this->failed_response($this->relative->errors, "409");
        }

        return $this->success_response("Relatives fetched successfully", RelativesResource::collection($relatives));
    }

    public function store(StoreRelativeRequest $request){
        if(!$relative  = $this->relative->store($request)){
            return $this->failed_response($this->relative->errors, "409");
        }

        return $this->success_response("Relative added successfully", new RelativesResource($relative));
    }

    public function show($id){
        if(!$relative = $this->relative->getRelative($id)){
            return $this->failed_response($this->relative->errors, "404");
        }

        return $this->success_response("Relative fetched successfully", new RelativesResource($relative));
    }

    public function update(UpdateRelativeRequest $request, $id){
        if(!$relative = $this->relative->updateRelative($request, $id)){
            return $this->failed_response($this->relative->errors, "409");
        }

        return $this->success_response("Relative Updated successfully", new RelativesResource($relative));
    }

    public function activation(UserActivationRequest $request, $uuid){
        if(!$this->relative->user_activation($request, $uuid)){
            return $this->failed_response($this->relative->errors, 404);
        }

        return $this->success_response("Operation successful");
    }

    public function destroy($id){
        if(!$this->relative->deleteRelative($id)){
            return $this->failed_response($this->relative->errors, "404");
        }

        return $this->success_response("Relative deleted successfully");
    }
}
