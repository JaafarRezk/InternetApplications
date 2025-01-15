namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class UserAddedToGroup implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $group;
    public $user;

    public function __construct($group, $user)
    {
        $this->group = $group;
        $this->user = $user;
    }

    public function broadcastOn()
    {
        return new Channel('group-'.$this->group->id);
    }

    public function broadcastAs()
    {
        return 'user-added';
    }
}
