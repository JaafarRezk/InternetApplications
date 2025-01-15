<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\UserNotificationEvent;
use App\Models\Group;
use App\Models\User;

class NotificationController extends Controller
{
    public function sendGroupInvitation($groupId, $invitedUserId)
    {
        // البحث عن المجموعة
        $group = Group::find($groupId);
        if (!$group) {
            return response()->json(['error' => 'Group not found.'], 404);
        }

        // البحث عن المستخدم
        $invitedUser = User::find($invitedUserId);
        if (!$invitedUser) {
            return response()->json(['error' => 'User not found.'], 404);
        }

        // المستخدم المرسل للدعوة
        $inviterId = auth()->id();

        // رسالة الإشعار
        $message = "You have been invited to join the group: {$group->name} by user ID {$inviterId}";

        // بث الإشعار
        broadcast(new UserNotificationEvent($message, $invitedUserId, $groupId, $inviterId))->toOthers();

        return response()->json(['message' => 'Invitation sent successfully!'], 200);
    }

    public function respondToInvitation(Request $request)
    {
        $groupId = $request->input('group_id');
        $response = $request->input('response'); // "accept" أو "reject"

        $user = auth()->user();

        $group = Group::find($groupId);
        if (!$group) {
            return response()->json(['error' => 'Group not found.'], 404);
        }

        if ($response === 'accept') {
            $group->users()->attach($user->id);
            return response()->json(['message' => 'Invitation accepted.'], 200);
        } elseif ($response === 'reject') {
            return response()->json(['message' => 'Invitation rejected.'], 200);
        } else {
            return response()->json(['error' => 'Invalid response.'], 400);
        }
    }
}
