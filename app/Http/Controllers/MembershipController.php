<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateMembershipInformationRequest;
use App\Http\Resources\MembershipResource;
use App\Http\Resources\MembershipTypeResource;
use App\Repositories\Interfaces\UserMembershipRepositoryInterface;
use Illuminate\Http\Request;

class MembershipController extends Controller
{
    protected $membership;

    public function __construct(UserMembershipRepositoryInterface $membership)
    {
        $this->membership = $membership;
    }

    public function types(){
        $types = $this->membership->membership_types();
        return $this->success_response("Membership Types fetched successfully", MembershipTypeResource::collection($types));
    }

    public function index(){
        $user = $this->membership->fetch_membership();
        return $this->success_response("Membership Information fetched successfully", new MembershipResource($user));
    } 
    
    public function update(UpdateMembershipInformationRequest $request){
        if(!$info = $this->membership->update_membership($request)){
            $this->failed_response($this->membership->errors, 409);
        }

        $this->success_response("Membership Information Updated successfully", new MembershipResource($info));
    }
}
