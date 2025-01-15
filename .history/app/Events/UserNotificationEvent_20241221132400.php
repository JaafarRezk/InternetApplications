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
    public $user_id;

    public function __construct($message, $user_id)
    {
        $this->message = $message;
        $this->user_id = $invitedUserId;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('private-notifications.' . $this->invitedUserId);
    }

    public function broadcastAs()
    {
        return 'group-invitation';
    }

    public function broadcastWith()
    {
        return [
            'message' => $this->message,
            'invitedUserId' => $this->invitedUserId,
        ];
    }
}
