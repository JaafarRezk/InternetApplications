<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class MyEvent
implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $group;
    public $user;
    public $status;

    /**
     * Create a new event instance.
     *
     * @param $group
     * @param $user
     * @param string $status
     */
    public function __construct($group, $user, $status = 'pending')
    {
        $this->group = $group;
        $this->user = $user;
        $this->status = $status;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return PrivateChannel
     */
    public function broadcastOn()
    {
        return new PrivateChannel('user-invitations-' . $this->user->id);
    }

    /**
     * Get the event name that is being broadcast.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'user-invitation';
    }
}
