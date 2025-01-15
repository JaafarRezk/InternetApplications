<?php

// app/Events/GroupInvitationEvent.php

namespace App\Events;

use App\Models\Group;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\BroadcastEvent;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class GroupInvitationEvent extends BroadcastEvent implements ShouldBroadcast
{
    public $group;
    public $invitedUserId;
    public $message;

    public function __construct(Group $group, $invitedUserId, $message)
    {
        $this->group = $group;
        $this->invitedUserId = $invitedUserId;
        $this->message = $message;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('privateInvite' . $this->group->id);
    }

    public function broadcastAs()
    {
        return 'group.invitation'; 
    }
    
}
