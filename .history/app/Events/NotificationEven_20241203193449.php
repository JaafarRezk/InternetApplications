<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationEven implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message; // محتوى الإشعار
    public $userId;  // معرّف المستخدم المستهدف

    /**
     * Create a new event instance.
     */
    public function __construct($message, $userId)
    {
        $this->message = $message;
        $this->userId = $userId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // قناة خاصة بمستخدم معين
        return [
            new PrivateChannel('user-' . $this->userId),
        ];
    }

    /**
     * Custom event name.
     */
    public function broadcastAs()
    {
        return 'notification-event';
    }
}
