<?php

// app/Events/UserNotificationEvent.php
namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class UserNotificationEvent implements ShouldBroadcast
{
    public $message;
    public $notification;

    public function __construct($message, $notification)
    {
        $this->message = $message;
        $this->notification = $notification;    }

    public function broadcastOn()
    {
        return new PrivateChannel('private-notifications.' . $this->notification['user_id']);
    }

    public function broadcastAs()
    {
        return 'group-invitation';
    }

    public function broadcastWith()
    {
        return [
            'message' => $this->message,
            'invitedUserId' => $this->notification,
        ];
    }
}
