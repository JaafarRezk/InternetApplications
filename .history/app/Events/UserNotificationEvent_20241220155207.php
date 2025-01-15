<?php

// app/Events/UserNotificationEvent.php
namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class UserNotificationEvent implements ShouldBroadcast
{
    public $message;
    public $invitedUserId;

    public function __construct($message, $invitedUserId)
    {
        $this->message = $message;
        $this->invitedUserId = $invitedUserId;
    }

    public function broadcastOn()
    {
        return new priChannel('private-notifications.' . $this->invitedUserId);
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
