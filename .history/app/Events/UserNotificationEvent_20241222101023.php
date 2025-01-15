<?php

// app/Events/UserNotificationEvent.php
namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Queue\SerializesModels;

class UserNotificationEvent implements ShouldBroadcast
{

    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $invitedUserId;

    public function __construct($message, $user_id)
    {
        $this->message = $message;
        $this->user_id = $user_id;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('private-notifications.' . $this->user_id);
    }

    public function broadcastAs()
    {
        return 'group-invitation';
    }

    public function broadcastWith()
    {
        return [
            'message' => $this->message,
            'invitedUserId' => $this->user_id,
        ];
    }
}
