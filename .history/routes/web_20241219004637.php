<?php

use App\Events\UserNotificationEvent;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\NotificationController;
use App\Models\Group;

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

    $inviterId = auth()->id();
    $group = Group::find($groupId);
    if (!$group) {
        return response()->json(['error' => 'Group not found.'], 404);
    }
    $message = "You have been invited to join the group: {$group->name} by user ID {$inviterId}";

    event(new UserNotificationEvent($inviterId,$message));
    return view('welcome');
});

