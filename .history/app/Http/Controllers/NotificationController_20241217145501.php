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

public function sendGroupInvitation($groupId, $invitedUserId)
{
    // تحقق من وجود المجموعة
    $group = Group::find($groupId);
    if (!$group) {
        return response()->json(['error' => 'Group not found.'], 404);
    }

    // تحقق من المستخدم المدعو
    $invitedUser = User::find($invitedUserId);
    if (!$invitedUser) {
        return response()->json(['error' => 'User not found.'], 404);
    }

    // إنشاء نص الرسالة
    $message = "You have been invited to join the group: {$group->name}";

    // بث الإشعار باستخدام الحدث
    broadcast(new UserNotificationEvent($message, $invitedUserId))->toOthers();

    return response()->json(['message' => 'Invitation sent successfully!'], 200);
}


}
