<?php

// app/Events/GroupInvitationEvent.php

namespace App\Events;

use Illuminate\Broadcasting\BroadcastEvent;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class GroupInvitationEvent extends BroadcastEvent implements ShouldBroadcast
{
   

    public function __construct()
    {
    
    }

    public function broadcastOn()
    {

    }

    public function broadcastAs()
    {
        
    }
    
}
