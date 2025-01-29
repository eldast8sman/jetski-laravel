<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

abstract class Controller
{
    public function success_response($message, $data=null){
        return response([
            'status' => "success",
            'message' => $message,
            'data' => $data
        ], 200);
    }

    public function failed_response($message, $status_code=500){
        return response([
            'status' => 'failed',
            'message' => $message
        ], $status_code);
    }

    public function test_data(){
        $faker = \Faker\Factory::create();

        return [
            'g5_id' => 634,
            'firstname' => "John",
            'lastname' => "Doe",
            'phone' => "08105657223",
            'email' => "omotolani@ropay.ng",
            'dob' => Carbon::parse("1922-02-19"),
            'gender' => "Male",
            'marital_status' => "Married",
            'address' => "Lagos",
            'membership_id' => null,
            'password' => Hash::make('ThinTree21+++'),
            'photo' => "https://avatars.dicebear.com/api/initials/" . "John" . ".svg",
        ];
    }
}
