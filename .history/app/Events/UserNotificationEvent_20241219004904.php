<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;

class UserNotificationEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $invitedUserId;
    public $groupId;

    public function __construct( $invitedUserId, $groupId)
    {
        $this->invitedUserId = $invitedUserId;
        $this->message = $message;
        $this->groupId = $groupId;
        $this->inviterId = $inviterId;
    }

    // تحديد القناة الخاصة لكل مستخدم
    public function broadcastOn()
    {
        return new PrivateChannel('private-notifications.' . $this->invitedUserId);
    }

    // اسم الحدث
    public function broadcastAs()
    {
        return 'group-invitation';
    }

    // البيانات المرسلة مع الإشعار
    public function broadcastWith()
    {
        return [
            'message' => $this->message,
            'group_id' => $this->groupId,
            'inviter_id' => $this->inviterId,
        ];
    }
}
