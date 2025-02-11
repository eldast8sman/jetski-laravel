<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ExcelUploadRequest;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateEmploymentInformationRequest;
use App\Http\Requests\Admin\UpdateMembershipInformationRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Http\Requests\Admin\UpdateWatercraftInformationRequest;
use App\Http\Requests\Admin\UserActivationRequest;
use App\Http\Resources\Admin\AllUserResource;
use App\Http\Resources\Admin\UserResource;
use App\Imports\MembershipImport;
use App\Models\User;
use App\Repositories\Interfaces\EmploymentDetailRepositoryInterface;
use App\Repositories\Interfaces\MemberRepositoryInterface;
use App\Repositories\Interfaces\MembershipInformationRepositoryInterface;
use App\Repositories\Interfaces\MenuRepositoryInterface;
use App\Repositories\Interfaces\UserWatercraftRepositoryInterface;
use App\Repositories\MenuRepository;
use App\Services\G5PosService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class MembershipController extends Controller
{
    protected $user;
    protected $info;
    protected $watercraft;
    protected $employment;
    protected $product;

    public function __construct(
        MemberRepositoryInterface $member,
        MembershipInformationRepositoryInterface $info,
        UserWatercraftRepositoryInterface $watercraft,
        EmploymentDetailRepositoryInterface $employment,
        MenuRepositoryInterface $product
    )
    {
        $this->user = $member;
        $this->info = $info;
        $this->watercraft = $watercraft;
        $this->employment = $employment;
        $this->product = $product;
    }

    public function index(Request $request){
        $limit = $request->has('limit') ? $request->limit : 20;
        $search = !empty($request->search) ? $request->search : "";
        $users = $this->user->index($limit, $search);

        return $this->success_response("Members fetched successfully", AllUserResource::collection($users)->response()->getData(true));
    }

    public function store_g5_members(){
        $users = $this->user->fetch_g5_customers();

        return $this->success_response("Users added to DB", $users);
    }

    public function resend_activation_link(User $user){
        if(!$this->user->resend_activation_link($user)){
            return $this->failed_response($this->user->errors, 400);
        }
        return $this->success_response("Email verification Link successfully sent");
    }

    public function store_bulk(ExcelUploadRequest $request){
        try {
            $file = $request->file('file');
            $path = $file->store('temp');

            Excel::import(new MembershipImport, $path);

            return $this->success_response("File Processing in progress");
        } catch(Exception $e){
            Log::error('Excel Error '. $e->getMessage());
            return $this->failed_response('Excel File Processing Failed', 500);
        }
    }

    public function show($uuid){
        if(empty($user = $this->find_uuid($uuid))){
            return $this->failed_response('No Member was fetched', 404);
        }

        return $this->success_response('Member fetched successfully', new UserResource($user));
    }

    private function find_uuid($uuid){
        return $this->user->fetch_member_by_param('uuid', $uuid);
    }

    public function update_membership_information(UpdateMembershipInformationRequest $request, $uuid){
        $user = $this->find_uuid($uuid);
        if(empty($user)){
            return $this->failed_response('No Member was fetched', 404);
        }

        $data = $request->all();
        if(isset($data['membership']) and !empty($data['membership'])){
            $product = $this->product->findFirstBy([
                'category' => 'Infrastructure',
                'name' => $data['membership']
            ]);
            if(!empty($product)){
                $this->user->update_user($user, ['membership_id' => $product->id]);
                $data['membership_id'] = $product->id;
            }
        }
        unset($data['membership']);
        if(!$this->info->store($data, $user->id)){
            return $this->failed_response($this->info->errors, 400);
        }

        return $this->success_response('Membership Information Updated', new UserResource($user));
    }

    public function update_watercraft_information(UpdateWatercraftInformationRequest $request, $uuid){
        $user = $this->find_uuid($uuid);
        if(empty($user)){
            return $this->failed_response('No Member was fetched', 404);
        }

        if(!$this->watercraft->store($request->all(), $user->id)){
            return $this->failed_response($this->watercraft->errors, 400);
        }

        return $this->success_response('Watercraft Information updated', new UserResource($user));
    }

    public function update_employment_information(UpdateEmploymentInformationRequest $request, $uuid){
        $user = $this->find_uuid($uuid);
        if(empty($user)){
            return $this->failed_response('No Member was fetched', 404);
        }

        if(!$this->employment->store($request->all(), $user->id)){
            return $this->failed_response($this->employment->errors, 400);
        }

        return $this->success_response('Employment Details updated successfully', new UserResource($user));
    }

    public function update(UpdateUserRequest $request, $uuid){
        $user = $this->find_uuid($uuid);
        if(empty($user)){
            return $this->failed_response('No Member was fetched', 404);   
        }

        if(!$updated = $this->user->update_member($request, $user)){
            return $this->failed_response($this->user->errors, 400);
        }

        return $this->success_response("User Profile updated successfull", new UserResource($updated));
    }

    public function store(StoreUserRequest $request){
        if(!$user = $this->user->store_user($request)){
            return $this->failed_response("User upload failed", 500);
        }

        return $this->success_response("User successfully added", new UserResource($user));
    }

    public function user_activation(UserActivationRequest $request, $uuid){
        if(!$this->user->user_activation($request, $uuid)){
            return $this->failed_response($this->user->errors, 404);
        }

        return $this->success_response("Operation successful");
    }

    public function add_test_user($uuid){
        $data = $this->test_data();
        $store = $this->user->store($data, 0);

        return $this->success_response("User added", $store);
    }
}
