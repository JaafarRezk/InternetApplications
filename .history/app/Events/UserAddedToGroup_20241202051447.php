<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class UserAddedToGroup implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $group;
    public $user;
    public $status;

    public function __construct($group, $user, $status = 'pending')
    {
        $this->group = $group;
        $this->user = $user;
        $this->status = $status;
    }

    public function broadcastOn()
    {
        return new Channel('user-invitations-' . $this->user->id);
    }

    public function broadcastAs()
    {
        return 'user-invitation';
    }
}
