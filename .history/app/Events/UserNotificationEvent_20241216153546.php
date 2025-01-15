<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Broadcasting\PrivateChannel;

class UserNotificationEvent 
{

    use Dispatchable, InteractsWithSockets, SerializesModels;
    
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

    public function broadcastWith()
    {
        return   (['message' => $this->message,
        'userId' => $this->userId,]);
    }

    public function broadcastAs()
    {
        return 'custom-user-notification-event';
    }
}
