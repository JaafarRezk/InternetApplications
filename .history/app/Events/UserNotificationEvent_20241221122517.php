<?php

// app/Events/UserNotificationEvent.php
namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Queue\SerializesModels;

class UserNotificationEvent implements ShouldBroadcast
{

    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $userId;

    /**
     * إنشاء حدث جديد
     *
     * @param string $message
     * @param int $userId
     */
    public function __construct(string $message, int $userId)
    {
        $this->message = $message;
        $this->userId = auth()->id();
    }

    /**
     * تحديد القناة التي سيتم بث الحدث عليها
     */
    public function broadcastOn()
    {
        return new PrivateChannel('private-notifications.' auth()->id());
    }

    /**
     * تحديد اسم الحدث عند البث
     */
    public function broadcastAs()
    {
        return 'user.notification';
    }

    /**
     * البيانات المرسلة مع الحدث
     */
    public function broadcastWith()
    {
        return [
            'message' => $this->message,
        ];
    }
}
