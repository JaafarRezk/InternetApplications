<?php

namespace App\Events;


use App\Models\Group;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GroupInvitationEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $group;
    public $user;
    public $invited_by;

    public function __construct(Group $group, User $user)
    {
        $this->group = $group;
        $this->user = $user;
        $this->invited_by = auth()->user()->name; // اسم الشخص الذي قام بدعوة المستخدم
    }

    public function broadcastOn()
    {
        return new PrivateChannel('user.' . $this->user->id);
    }

    public function broadcastWith()
    {
        return [
            'group_id' => $this->group->id,
            'group_name' => $this->group->name,
            'invited_by' => $this->invited_by,
        ];
    }
}
