<?php

namespace App\Http\Controllers;

use App\Http\Requests\ActivateAccountRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\ProfilePhotoRequest;
use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Resources\LoginResource;
use App\Http\Resources\ProfileResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\v1\Users\Account\AccountResource;
use App\Mail\Admin\ForgotPasswordMail;
use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Services\AuthService;
use App\Services\G5PosService;
use Exception;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    private $auth;
    protected $user;

    public function __construct(UserRepositoryInterface $user)
    {
        $this->auth = new AuthService('user-api');
        $this->user = $user;
    }

    public function fetch_token($token){
        if(empty($user = $this->user->findByVerificationToken($token))){
            return $this->failed_response("Wrong Link", 404);
        }

        return $this->success_response("Account fetched successfully", $user);
    }

    public function activate_account(ActivateAccountRequest $request){
        try {
            if(!$user = $this->user->activate($request)){
                return $this->failed_response($this->user->errors, 409);
            }
            $user->authorization = $this->auth->login($user);

            return $this->success_response("Email successfully Verified", $user);
        } catch(Exception $e){
            return $this->failed_response($e->getMessage());
        }
    }

    public function login(LoginRequest $request){
        if(!$token = $this->auth->attempt($request->all())){
            return $this->failed_response($this->auth->errors, 400);
        }
        $user = $this->user->findByEmail($request->email);
        $user->authorization = $token;
        return $this->success_response("Login successful", new LoginResource($user));
    }

    public function refresh_token(){
        if(!$token = $this->auth->refresh_token()){
            return $this->failed_response("Login Expired", 400);
        }

        return $this->success_response("Token refreshed successfuly", $token);
    }

    public function forgot_password(ForgotPasswordRequest $request)
    {
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

    public function me(){
        $user = $this->user->find($this->auth->logged_in_user()->id);
        return $this->success_response('Profile fetched successfully', new UserResource($user));
    }

    public function change_profile_photo(ProfilePhotoRequest $request){
        if(!$user = $this->user->update_photo($request)){
            return $this->failed_response("Photo Upload Failed");
        }

        return $this->success_response('Profile Photo Updated successfully', new UserResource($user));
    }

    public function update(ProfileUpdateRequest $request){
        if(($request->gender != 'Male') and ($request->gender != 'Female')){
            return $this->failed_response('Wrong Gender', 422);
        }
        if(!$user = $this->user->update($this->auth->logged_in_user()->id, $request->all())){
            return $this->failed_response($this->user->error_msg);
        }

        return $this->success_response("Profile Update successful", new UserResource($user));
    }

    public function change_password(ChangePasswordRequest $request){
        if(!$this->auth->change_password($request)){
            return $this->failed_response($this->auth->errors, 409);
        }
        return $this->success_response("Password successfully changed");
    }

    public function resend_otp(Request $request){
        if(!$user = $this->user->resend_activation_link($request)){
            return $this->failed_response($this->user->errors, 400);
        }
        return $this->success_response("OTP has been resent to ".$user->email);
    }

    public function get_user_g5_orders($user_id){
        $user = User::find($user_id);
        $g5 = new G5PosService();

        $orders = $g5->getOrders(['CustomerID' => $user->g5_id]);
        return $this->success_response("Orders fetched", json_decode($orders, true));
    }
}
