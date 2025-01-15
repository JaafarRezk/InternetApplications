<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\NotificationController;
use App\Events\NewNotification;
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
    return view('welcome');
});
Route::post('/send-notification', function (Request $request) {
    $message = $request->input('message');
    broadcast(new NewNotification($message))->toOthers();
    return response()->json(['status' => 'Notification sent!']);
});