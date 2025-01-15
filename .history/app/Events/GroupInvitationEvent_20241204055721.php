<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GroupInvitationEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public $groupName;
    public $inviterName;

    public function __construct($groupName, $inviterName)
    {
        $this->groupName = $groupName;
        $this->inviterName = $inviterName;
    }

    public function broadcastOn()
    {
        return new PrivateChannel(`private-user-${userId}`); // قناة خاصة للمستخدم المستهدف
    }

    public function broadcastAs()
    {
        return 'group-invitation';
    }
}
