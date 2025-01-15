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

    public $invitedUserId;
    public $message;
    public $groupId;
    public $inviterId;

    public function __construct($message)
    {
        $this->message = $message;
  
    }

    // تحديد القناة الخاصة لكل مستخدم
    public function broadcastOn()
    {
        return new Channel('notifications.' . $this->invitedUserId);
    }

    // اسم الحدث
    public function broadcastAs()
    {
        return 'group-invitation';
    }

    // البيانات المرسلة مع الإشعار
    public function broadcastWith()
    {
        return [
            'message' => $this->message,
      
        ];
    }
}
