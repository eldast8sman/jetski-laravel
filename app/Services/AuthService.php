<?php

namespace App\Services;

use App\Jobs\BulkOrderJob;
use App\Mail\Admin\ForgotPasswordMail;
use App\Models\Admin;
use App\Models\Order;
use App\Models\User;
use App\Repositories\OrderRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use PDO;

class AuthService
{
    private $guard;
    public $errors = "";

    public function __construct($guard='user-api'){
        $this->guard = $guard;
    }

    public function attempt($data) : array|bool
    {
        if(!$token = auth($this->guard)->attempt($data)){
            $this->errors = "Wrong Credentials";
            return false;
        }

        if($this->guard == 'admin-api'){
            $user = Admin::where('email', $data['email'])->first();
        } elseif($this->guard == 'user-api') {
            $user = User::where('email', $data['email'])->first();
        }
        if(($this->guard == 'user-api') and ($user->can_use != 1)){
            $this->errors = "Your account is disabled. Please contact your sponsor or Admin";
            return false;
        }
        $user->prev_login = $user->last_login;
        $user->last_login = date('Y-m-d H:i:s');
        $user->save();

        if($this->guard == 'user-api'){
            if(($user->next_order_sync <= date('Y-m-d H:i:s') or ($user->next_order_sync == null))){
                BulkOrderJob::dispatch($user->id);
            }
        }

        return [
            'token' => $token,
            'type' => 'Bearer',
            'expires' => env('JWT_TTL') * 60
        ];
    }

    public function logout() : void
    {
        auth($this->guard)->logout();
    }

    public function logged_in_user(){
        return auth($this->guard)->user();
    }

    public function login($user) : array|bool
    {
        if(!$token = auth($this->guard)->login($user)){
            return false;
        }

        if($this->guard == 'admin-api'){
            $user = Admin::find($user->id);
        } elseif($this->guard == 'user-api') {
            $user = User::find($user->id);
        }
        $user->prev_login = $user->last_login;
        $user->last_login = date('Y-m-d H:i:s');
        $user->save();

        if($this->guard == 'user-api'){
            if(($user->next_order_sync <= date('Y-m-d H:i:s') or ($user->next_order_sync == null))){
                BulkOrderJob::dispatch($user->id);
            }
        }
        

        return [
            'token' => $token,
            'type' => 'Bearer',
            'expires' => env('JWT_TTL') * 60
        ];
    }

    public function refresh_token() : array|bool
    {
        try {
            $token = auth($this->guard)->refresh();

            $data = [
                'token' => $token,
                'type' => 'Bearer',
                'expires' => env('JWT_TTL') * 60
            ];

            return $data;
        } catch(Exception $e){
            return false;
        }
    }

    public function forgot_password(Request $request)
    {
        if($this->guard == 'admin-api'){
            $user = Admin::where('email', $request->email)->first();
        } elseif($this->guard == 'user-api'){
            $user = User::where('email', $request->email)->first();
        }
        if(empty($user)){
            return false;
        } 
        
        $user->token = rand(111111, 999999);
        $user->token_expiry = date('Y-m-d H:i:s', time() + (60 * 15));
        $user->save();

        Mail::to($user->email)->send(new ForgotPasswordMail($user->firstname, $user->token));
        return true;
    }

    public function reset_password(Request $request) : bool
    {
        if($this->guard == 'admin-api'){
            $user = Admin::where('token', $request->token)->where('email', $request->email)->first();
        } elseif($this->guard == 'user-api'){
            $user = User::where('token', $request->token)->where('email', $request->email)->first();
        }
        if(empty($user)){
            $this->errors = "Wrong OTP";
            return false;
        }

        if($user->token_expiry < date('Y-m-d H:i:s')){
            $this->errors = "Expired OTP";
            return false;
        }
        $user->update([
            'token' => null,
            'token_expiry' => null,
            'password' => Hash::make($request->password)
        ]);

        return true;
    }
    
    public function change_password(Request $request) : bool
    {
        if($this->guard == 'admin-api'){
            $user = Admin::find($this->logged_in_user()->id);
        } elseif($this->guard == 'user-api'){
            $user = User::find($this->logged_in_user()->id);
        }

        if(!Hash::check($request->old_password, $user->password)){
            $this->errors = "Incorrect Old Password";
            return false;
        }
        $user->password = Hash::make($request->password);
        $user->save();

        return true;
    }
}