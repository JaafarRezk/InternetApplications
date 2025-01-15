<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
;

class UserNotificationEvent mplements ShouldBroadcast
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
        return new PrivateChannel('user-notification.' . $this->receiverId);
    }

    public function broadcastWith()
    {
        return   (['message' => $this->message,
        'userId' => $this->receiverId,]);
    }

    public function broadcastAs()
    {
        return 'custom-user-notification-event';
    }
}
