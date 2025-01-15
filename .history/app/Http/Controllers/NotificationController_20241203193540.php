<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\NewNotification;
use App\Models\Group;
use App\Events\GroupInvitationEvent;

class NotificationController extends Controller
{
    
    public function sendNotification(Request $request)
{
    $message = $request->input('message');
    event(new NotificationEven('This is a notification message!', $user->id));
    return response()->json(['status' => 'Notification sent!']);
}



}
