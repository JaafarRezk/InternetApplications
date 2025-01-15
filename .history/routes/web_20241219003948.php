<?php

use App\Events\UserNotificationEvent;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\NotificationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::get('/',function(){

    UserNotificationEvent::dispatch()
    return view('welcome');
});

Route::get('/notify',function(){
    return view('notify');
});

Route::post('/notify',function(){
    return view('notify');
});