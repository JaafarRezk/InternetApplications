<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
;

class UserNotificationEvent 
{

    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $receiverId;

    public function __construct($message, $receiverId)
    {
        $this->message = $message;
        $this->receiverId = $receiverId;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('user-notification.' . $this->usreceiverIderId);
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
