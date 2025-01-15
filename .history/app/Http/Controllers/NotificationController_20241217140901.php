<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\NewNotification;
use App\Models\Group;
use App\Events\GroupInvitationEvent;
use App\Events\UserNotificationEvent;

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

public function sendNotification(Request $request)
{
//    $userId = $request->input('user_id');
  //  $message = $request->input('message');
  $message = 'Hello, this is a real-time notification!';
    // بث الإشعار
    broadcast(new UserNotificationEvent($message));

    return response()->json(['message' => 'Notification sent successfully.']);
}


}
