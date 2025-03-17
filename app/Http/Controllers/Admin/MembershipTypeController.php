<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreMembershipTypeRequest;
use App\Http\Resources\Admin\MembershipTypeResource;
use App\Repositories\Interfaces\MembershipTypeRepositoryInterface;
use Illuminate\Http\Request;

class MembershipTypeController extends Controller
{
    protected $type;

    public function __construct(MembershipTypeRepositoryInterface $type)
    {
        $this->type = $type;
    }

    public function store_default(){
        $types = file_get_contents(base_path('/data/membership_types.json'));
        $types = json_decode($types, true);

        foreach($types as $type){
            $found = $this->type->findFirstBy([
                'name' => $type['name']
            ]);

            if(empty($found)){
                $found = $this->type->store($type);
                if(!$found){
                    return $this->failed_response($this->type->errors);
                }
            } else {
                $this->type->update($found->id, $type);
            }
        }

        return $this->success_response("Default Membership Types uploaded");
    }

    public function store(StoreMembershipTypeRequest $request){
        if(!$type = $this->type->store($request->all())){
            return $this->failed_response($this->type->errors);
        }
        return $this->success_response("Membership Type uploaded successfully", new MembershipTypeResource($type));
    }

    public function index(){
        $types = $this->type->all([['name', 'asc']]);
        return $this->success_response("Order Types fetched successfully", MembershipTypeResource::collection($types));
    }

    public function update(StoreMembershipTypeRequest $request, $uuid){
        if(empty($type = $this->type->findByUuid($uuid))){
            return $this->failed_response("No Membership Type was fetched", 404);
        }

        $type = $this->type->update($type->id, $request->all());
        if(!$type){
            return $this->failed_response('Update Failed', 500);
        }

        return $this->success_response("Membership Type updated successfully", new MembershipTypeResource($type));
    }

    public function destroy($uuid){
        if(empty($type = $this->type->findByUuid($uuid))){
            return $this->failed_response("No membership was fetched", 404);
        }
        $type->delete();

        return $this->success_response("membership Type deleted successfully");
    }
}
