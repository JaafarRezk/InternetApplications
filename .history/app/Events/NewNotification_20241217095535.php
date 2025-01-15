<?php
namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userId;
    public $message;

    public function __construct($userId, $message)
    {
        $this->userId = $userId;
        $this->message = $message;
    }

    // تحديد القناة التي سيتم البث عليها
    public function broadcastOn()
    {
        return new Channel('private-channel.' . $this->userId);
    }

    // تحديد اسم الحدث
    public function broadcastAs()
    {
        return 'notification-event';
    }
}
