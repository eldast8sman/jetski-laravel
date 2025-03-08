<?php

use App\Http\Controllers\InviteController;
use Illuminate\Support\Facades\Route;

Route::controller(InviteController::class)->group(function(){
    Route::get('/booking-invite/{id}', 'show');
    Route::post('/booking-invite/{id}', 'store')->name('accept.invite');
    
});

Route::get('/', function(){
    return view('welcome');
});
