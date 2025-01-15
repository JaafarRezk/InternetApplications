<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\NewNotification;
use App\Models\Group;
use App\Events\GroupInvitationEvent;
use App\Events\UserNotificationEvent;
use App\Models\User;

class NotificationController extends Controller
{
    /*
    public function sendNotification(Request $request)
{
    $message = $request->input('message');
    broadcast(new NewNotification($message))->toOthers();
    return response()->json(['status' => 'Notification sent!']);
}


*/

public function sendNotification()
{
    $message = "Hello, this is a private notification!";
    event(new User($message));
    return response()->json(['message' => 'Notification sent!']);
}
}
