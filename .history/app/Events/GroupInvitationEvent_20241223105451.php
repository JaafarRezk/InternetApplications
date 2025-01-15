<?php

namespace App\Events;

use App\Models\Group;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GroupInvitationEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $group;
    public $invitedUserId;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Group $group, User $invitedUser)
    {
        \Log::info('Group data:', ['group' => $group]);
        \Log::info('Invited User ID:', ['invitedUserId' => $invitedUser]);
    
        $this->group = $group; // الكائن Group مباشرة
        $this->invitedUserId = $invitedUser->id; // استخراج معرف المستخدم فقط
    }
    

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('private-user-' . $this->invitedUserId);
    }

    public function broadcastAs()
  {
      return 'GroupInvitationEvent';
  }

 
  
}
