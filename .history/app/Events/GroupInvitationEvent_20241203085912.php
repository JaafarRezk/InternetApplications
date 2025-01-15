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

    // إعداد البيانات التي سيتم إرسالها عبر WebSockets
    public function __construct(Group $group, User $user, User $invited_by)
    {
        $this->group = $group;
        $this->user = $user;
        $this->invited_by = $invited_by;
    }

    // تحديد القناة التي سيتم بث الدعوة عليها
    public function broadcastOn()
    {
        return new PrivateChannel('user.' . $this->user->id);
    }

    // تخصيص البيانات التي سيتم بثها عبر Pusher
    public function broadcastWith()
    {
        return [
            'group_name' => $this->group->name,
            'invited_by' => $this->invited_by->name,
            'group_id' => $this->group->id,
            'invitation_url' => route('group.invite.accept', ['group_id' => $this->group->id, 'user_id' => $this->user->id])
        ];
    }
}
