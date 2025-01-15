<?php

// app/Events/UserNotificationEvent.php
namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
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
        return new PrivateChannel('private-notifications.{userId}' . $this->invitedUserId);
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
