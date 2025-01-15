<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GroupInvitationNotification
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

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

    public function via($notifiable)
    {
        return ['broadcast', 'database']; // نشر الإشعار عبر Broadcast و Database
    }
    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
