<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\

class UserInvitationEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $group;
    public $invitedUserId;


    public function __construct(Group $group, User $invitedUser)
    {
        \Log::info('Group data:', ['group' => $group]);
        \Log::info('Invited User ID:', ['invitedUserId' => $invitedUser]);
    
        $this->group = $group; // الكائن Group مباشرة
        $this->invitedUserId = $invitedUser->id; // استخراج معرف المستخدم فقط
    }
    

   
    public function broadcastOn()
    {
        return new PrivateChannel('private-user-' . $this->invitedUserId);
    }

    public function broadcastAs()
  {
      return 'GroupInvitationEvent';
  }
}
