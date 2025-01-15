<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\NewNotification;

class NotificationController extends Controller
{
    
    public function sendNotification(Request $request)
{
    $message = $request->input('message');
    broadcast(new NewNotification($message))->toOthers();
    return response()->json(['status' => 'Notification sent!']);
}

}
