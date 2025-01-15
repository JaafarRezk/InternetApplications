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


public function inviteUsers(Request $request, $groupId)
{
    // افترض أن لديك مجموعة
    $group = Group::findOrFail($groupId);

    // معرفات المستخدمين المدعوين
    $invitedUserIds = $request->input('invited_user_ids', []);

    // رسالة الدعوة
    $message = "You have been invited to join the group {$group->name}";

    // إرسال الحدث
    event(new GroupInvitationEvent($group, $invitedUserIds, $message));

    return response()->json(['message' => 'Invitations sent successfully!']);
}
}
