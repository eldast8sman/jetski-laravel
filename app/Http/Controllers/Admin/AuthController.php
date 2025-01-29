<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ActivateAccountRequest;
use App\Http\Requests\Admin\ChangeAccountDetailsRequest;
use App\Http\Requests\Admin\ChangePasswordRequest;
use App\Http\Requests\Admin\ForgotPasswordRequest;
use App\Http\Requests\Admin\LoginRequest;
use App\Http\Requests\Admin\ResetPasswordRequest;
use App\Http\Requests\Admin\UpdateProfileRequest;
use App\Http\Resources\Admin\LoginResource;
use App\Models\Admin;
use App\Repositories\Interfaces\AdminRepositoryInterface;
use App\Services\AuthService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    private $auth;
    protected $admin;

    public function __construct(AdminRepositoryInterface $admin)
    {
        $this->admin = $admin;
        $this->auth = new AuthService('admin-api');
    }

    public function store(Request $request){
        if(Admin::count() > 0){
            return $this->failed_response("There is already an Admin", 409);
        }
        $admin = $this->admin->store($request);
        if(!$admin){
            return $this->failed_response("Failed to add Admin", 500);
        }

        return $this->success_response("Admin added successfully", $admin);
    }

    public function fetch_token($token){
        if(empty($admin = $this->admin->findFirstBy(['verification_token' => $token]))){
            return $this->failed_response("No Account was fetched", 404);
        }

        return $this->success_response("Account fetched successfully", $admin);
    }

    public function fetch_by_verification_token($token){
        try {
            $admin = $this->admin->findByVerificationToken($token);
            if(empty($admin)){
                return $this->failed_response("Wrong Link", 404);
            }
            return $this->success_response("Admin fetched successfully", $admin);
        } catch (Exception $e){
            return $this->failed_response("Server Error");
        }
    }

    public function activate_account(ActivateAccountRequest $request){
        try {
            $admin = $this->admin->activate($request);
            if(!$admin){
                return $this->failed_response($this->admin->errors, 409);
            }
            $admin->authorization = $this->auth->login($admin);
    
            return $this->success_response("Account activated succssfully", $admin);
        } catch(Exception $e){
            return $this->failed_response("Server Error");
        }
    }

    public function login(LoginRequest $request){
        $all = $request->all();
        if(!$token = $this->auth->attempt($all)){
            return $this->failed_response("Wrong Login Credentials");
        }
        $admin = $this->admin->findByEmail($request->email);
        $admin->authorization = $token;
        return $this->success_response("LogIn successful", new LoginResource($admin));
    }

    public function refresh_token(){
        if(!$token = $this->auth->refresh_token()){
            return $this->failed_response("Login Expired", 409);
        }

        return $this->success_response("Token successfully refreshed", $token);
    }

    public function forgot_password(ForgotPasswordRequest $request){
        if(!$this->auth->forgot_password($request)){
            return $this->failed_response("Wrong Email", 404);
        }
        return $this->success_response("Reset Password Link sent to ".$request->email);
    }

    public function reset_password(ResetPasswordRequest $request){
        if(!$this->auth->reset_password($request)){
            return $this->failed_response($this->auth->errors, 409);
        }

        return $this->success_response("Password changed successfully");
    }

    public function change_password(ChangePasswordRequest $request){
        if(!$this->auth->change_password($request)){
            return $this->failed_response("Incorrect Old Password", 409);
        }

        return $this->success_response("Password successfully changed");
    }

    public function update_account_details(ChangeAccountDetailsRequest $request){
        $admin = $this->admin->update_account_details($this->auth->logged_in_user(), $request);

        return $this->success_response("Account details successfully updated", $admin);
    }

    public function update(UpdateProfileRequest $request){
        if(!$admin = $this->admin->update_profile($this->auth->logged_in_user(), $request)){
            return $this->failed_response($this->admin->errors);
        }

        return $this->success_response('Profile Updated successfully', $admin);
    }

    public function me(){
        return $this->success_response("Logged in user details fetched successfully", $this->admin->admin($this->auth->logged_in_user()));
    }
}
