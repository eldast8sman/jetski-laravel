<?php

namespace App\Repositories;

use App\Mail\AddUserNotificationMail;
use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Services\FileManagerService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class UserRepository extends AbstractRepository implements UserRepositoryInterface
{
    public $errors = "";

    public function __construct(User $user)
    {
        parent::__construct($user);
    }

    public function findByVerificationToken(string $token)
    {
        if(!$user = $this->findFirstBy(['verification_token' => $token])){
            return false;
        }

        return $user;
    }

    public function activate(Request $request)
    {
        try {
            if(empty($user = $this->findFirstBy(['verification_token' => $request->token, 'email_verified' => 0, 'email' => $request->email]))){
                $this->errors = "Wrong Link";
                return false;
            }
            if(strtotime($user->verification_token_expiry) < time()){
                $user->verification_token = mt_rand(111111, 999999);
                $user->verification_token_expiry = date('Y-m-d H:i:s', time() + (60 * 60 * 24));
                $user->save();
                $user->name = $user->firstname;
                Mail::to($user)->send(new AddUserNotificationMail($user->name, $user->verification_token, $user->email));
                $this->errors = "Expired OTP and an updated OTP sent to ".$user->email;
                return false;
            }

            $user->verification_token = null;
            $user->verification_token_expiry = null;
            $user->password = bcrypt($request->password);
            $user->email_verified = 1;
            $this->save($user);

            return $user;
        } catch(Exception $e){
            $this->errors = $e->getMessage();
            return false;
        }
    }

    public function findByEmail(string $email)
    {
        $user = $this->findFirstBy(['email' => $email]);
        if(!$user){
            return false;
        }
        return $user;
    }

    public function update_photo(Request $request)
    {
        $photo = FileManagerService::upload_file($request->file('photo'), env('FILESYSTEM_DISK'));
        if(!$photo){
            return false;
        }

        $user = $this->find(auth('user-api')->user()->id);
        $old_photo = $user->photo;
        $user->photo = $photo->url;
        $this->save($user);
        if(!empty($old_photo)){
            $old_photo = FileManagerService::findByUrl($old_photo);
            if($old_photo){
                FileManagerService::delete($old_photo->id);
            }
        }

        return $user;
    }

    public function resend_activation_link(Request $request)
    {
        $user = $this->findFirstBy(['email' => $request->email]);
        if(empty($user)){
            $this->errors = "Wrong Email";
            return false;
        }

        if($user->email_verified == 1){
            $this->errors = "Email already verified";
            return false;
        }

        $user->verification_token = mt_rand(111111, 666666);
        $user->verification_token_expiry = date('Y-m-d H:i:s', time() + (60 * 60 * 24));
        $user->save();
        $user->name = $user->firstname;
        Mail::to($user)->send(new AddUserNotificationMail($user->name, $user->verification_token, $user->email));

        return $user;
    }
}