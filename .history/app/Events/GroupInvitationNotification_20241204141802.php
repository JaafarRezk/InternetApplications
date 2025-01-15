<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class GroupInvitationNotification extends Notification
{
    public $groupName;
    public $inviterName;
    public $invitationId;

    // تمرير اسم المجموعة واسم المستخدم الذي دعا ورقم التعريف الفريد
    public function __construct($groupName, $inviterName, $invitationId)
    {
        $this->groupName = $groupName;
        $this->inviterName = $inviterName;
        $this->invitationId = $invitationId;
    }

    // استخدام البث عبر Pusher
    public function via($notifiable)
    {
        return ['broadcast', 'database']; // نشر الإشعار عبر Broadcast و Database
    }

    // بيانات الإشعار عبر البث
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'group_name' => $this->groupName,
            'inviter_name' => $this->inviterName,
            'invitation_id' => $this->invitationId,
            'message' => $this->inviterName . ' invited you to join the group ' . $this->groupName,
        ]);
    }

    // إرسال إشعار إلى قاعدة البيانات (اختياري)
    public function toDatabase($notifiable)
    {
        return [
            'group_name' => $this->groupName,
            'inviter_name' => $this->inviterName,
            'message' => $this->inviterName . ' invited you to join the group ' . $this->groupName,
            'invitation_id' => $this->invitationId,
            'action_url' => url("/group/invite/{$this->invitationId}/accept"),  // رابط الموافقة
            'reject_url' => url("/group/invite/{$this->invitationId}/reject")   // رابط الرفض
        ];
    }
}
