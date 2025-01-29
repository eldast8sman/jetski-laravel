<?php

use App\Http\Controllers\Admin\MembershipController;
use App\Models\Product;
use App\Models\User;
use App\Repositories\MemberRepository;
use App\Repositories\MenuRepository;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Schedule::call(function(){
//     $repo = new MemberRepository(new User());
//     $repo->fetch_g5_customers();
// })->twiceDaily();

// Schedule::call(function(){
//     $repo = new MenuRepository(new Product());
//     $repo->fetch_g5_menu();
// })->twiceDaily();
