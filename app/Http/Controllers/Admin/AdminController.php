<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAdminRequest;
use App\Http\Requests\Admin\UpdateAdminRequest;
use App\Models\Admin;
use App\Repositories\Interfaces\AdminRepositoryInterface;
use App\Services\AuthService;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    protected $admin;
    private $user;

    public function __construct(AdminRepositoryInterface $admin)
    {
        $this->admin = $admin;
        $auth = new AuthService('admin-api');
        $this->user = $auth->logged_in_user();
    }

    private function check_role(){
        if($this->user->role != 'super'){
            return false;
        }

        return true;
    }

    public function index(){
        $admins = $this->admin->all_admins();

        return $this->success_response("Admins fetched successfully", $admins);
    }

    public function store(StoreAdminRequest $request){
        if(!$this->check_role()){
            return $this->failed_response("Not Authorised", 402);
        }

        if(!$admin = $this->admin->store($request)){
            return $this->failed_response("Admin Creation Failed", 500);
        }

        return $this->success_response("Admin created successfully", $admin);
    }

    public function show($uuid){
        if(!$admin = $this->admin->fetch_by_uuid($uuid)){
            return $this->failed_response("No Admin was fetched", 404);
        }

        return $this->success_response("Admin fetched successfully", $admin);
    }

    public function update(UpdateAdminRequest $request, $uuid){
        if(!$this->check_role()){
            $this->failed_response("Not Authorised", 402);
        }
        if(!$admin = $this->admin->update_admin($uuid, $request)){
            return $this->failed_response($this->admin->errors, 409);
        }

        return $this->success_response("Admin successfully updated", $admin);
    }

    public function destroy($uuid){
        if(!$this->check_role()){
            return $this->failed_response("Not Authorised", 402);
        }
        if($uuid == $this->user->uuid){
            return $this->failed_response("You cannot delete yourself", 409);   
        }
        if(!$this->admin->delete_admin($uuid)){
            return $this->failed_response($this->admin->errors, 409);
        }

        return $this->success_response("Admin deleted successfully");
    }
}
