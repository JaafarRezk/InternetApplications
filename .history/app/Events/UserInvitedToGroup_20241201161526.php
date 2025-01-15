namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class UserInvitedToGroup implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $group;
    public $invitedUser;

    /**
     * Create a new event instance.
     */
    public function __construct($group, $invitedUser)
    {
        $this->group = $group;
        $this->invitedUser = $invitedUser;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn()
    {
        return new Channel('group-invitations');
    }

    /**
     * Data to send to Pusher.
     */
    public function broadcastWith()
    {
        return [
            'group' => $this->group,
            'invitedUser' => $this->invitedUser,
        ];
    }
}
