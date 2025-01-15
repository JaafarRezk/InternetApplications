<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Broadcasting\PrivateChannel;

class UserNotificationEvent implements ShouldBroadcast
{
    public $message;
    public $userId;

    public function __construct($message, $userId)
    {
        $this->message = $message;
        $this->userId = $userId;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('user-notification.' . $this->userId);
    
    }
    

    public function broadcastAs()
    {
        return 'user-notification-event';
    }
}
