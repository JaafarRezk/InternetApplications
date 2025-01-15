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
    public function __construct(Group $group,  $invitedUserId)
    {
        $this->group = $group;
        $this->invitedUserId = $invitedUserId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return PrivateChannel('private-user-' . $this->invitedUserId]);
    }

    public function broadcastAs()
  {
      return 'GroupInvitationEvent';
  }

    public function broadcastWith()
    {
        return [
            'group' => [
                'id' => $this->group->id,
                'name' => $this->group->name,
            ],
            'invited_by' => [
                'id' => auth()->id(),
                'name' => auth()->user()->name,
            ],
        ];
    }
}
