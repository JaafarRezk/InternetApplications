<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;

class GroupInvitationEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $groupName;
    public $invitedUserId;

    public function __construct($groupName, $invitedUserId)
    {
        $this->groupName = $groupName;
        $this->invitedUserId = $invitedUserId;
    }

    public function broadcastOn()
    {
        // بث على قناة خاصة لكل مستخدم مدعو
        return new PrivateChannel("notifications.{$this->invitedUserId}");
    }

    public function broadcastAs()
    {
        return 'group-invitation-event';
    }

    public function broadcastWith()
    {
        return [
            'message' => "You have been invited to join the group: {$this->groupName}",
            'group_name' => $this->groupName,
        ];
    }
}
