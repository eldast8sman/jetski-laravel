<?php

use App\Http\Controllers\Admin\MembershipController;
use App\Http\Controllers\SparkleController;
use App\Models\Product;
use App\Models\User;
use App\Repositories\MemberRepository;
use App\Repositories\MenuRepository;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;


// Schedule::call(function(){
//     $repo = new MemberRepository(new User());
//     $repo->fetch_g5_customers();
// })->twiceDaily();

Schedule::call(function(){
    $sparkle = new SparkleController(new \App\Services\SparkleService());
    $sparkle->fetch_transactions();
    Log::info("Fetching transactions");
})->everyMinute();
