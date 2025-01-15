<?php
namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MyEvent implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public $user;
    public $message;

    public function __construct($user, $message)
    {
        $this->user = $user; // المستخدم المدعو
        $this->message = $message; // البيانات الخاصة بالدعوة
    }

    public function broadcastOn()
    {
        // بث الإشعار عبر قناة خاصة بكل مستخدم بناءً على معرفه
        return new PrivateChannel('my-channel.' . $this->user->id); // قناة خاصة بكل مستخدم
    }

    public function broadcastAs()
    {
        return 'my-event'; // اسم الحدث الذي سيتم بثه
    }
}
