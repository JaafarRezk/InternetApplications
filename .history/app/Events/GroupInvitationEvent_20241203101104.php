<?php

namespace App\Events;

use App\Models\Group;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class GroupInvitationEvent implements ShouldBroadcast
{
    public $group;
    public $invitedUserIds;
    public $message;

    /**
     * Create a new event instance.
     *
     * @param Group $group
     * @param array $invitedUserIds
     * @param string $message
     */
    public function __construct(Group $group, array $invitedUserIds, string $message)
    {
        $this->group = $group;
        $this->invitedUserIds = $invitedUserIds;
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        // قناة خاصة بكل مستخدم مدعو
        $channels = [];
        foreach ($this->invitedUserIds as $userId) {
            $channels[] = new PrivateChannel('user.' . $userId);
        }

        return $channels;
    }

    /**
     * The name of the event for broadcasting.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'group.invitation';
    }
}
