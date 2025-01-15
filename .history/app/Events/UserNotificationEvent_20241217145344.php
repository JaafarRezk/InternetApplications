<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;

class UserNotificationEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userId;
    public $message;

    public function __construct($message)
    {
        $this->userId = $userId;
        $this->message = $message;
    }

  // تحديد القناة الخاصة لكل مستخدم
public function broadcastOn()
{
    return new PrivateChannel('notifications.' . $this->userId);
}

    public function broadcastAs()
    {
        return 'my-event';// important part of realtime notification
    }

    public function broadcastWith()
    {
        return ['message' => $this->message]; // البيانات التي سيتم بثها
    }
}
