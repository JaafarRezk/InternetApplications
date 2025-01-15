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

public function sendGroupInvitation($groupId, $invitedUserId)
{
    // جلب المجموعة والمستخدم
    $group = Group::find($groupId);
    if (!$group) {
        return response()->json(['error' => 'Group not found.'], 404);
    }

    $invitedUser = User::find($invitedUserId);
    if (!$invitedUser) {
        return response()->json(['error' => 'User not found.'], 404);
    }

    // بث الإشعار لدعوة المستخدم إلى المجموعة
    broadcast(new GroupInvitationEvent($group->name, $invitedUserId));

    return response()->json(['message' => 'Invitation sent successfully!'], 200);
}



}
