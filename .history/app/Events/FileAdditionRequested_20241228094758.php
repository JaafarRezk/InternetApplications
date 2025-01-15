<?php
namespace App\Events;

use App\Models\Group;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FileAdditionRequested implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $group;
    public $files_ids;

    public function __construct(Group $group, array $files_ids)
    {
        $this->group = $group;
        $this->files_ids = $files_ids;
    }

    public function broadcastOn()
    {
        // قناة خاصة للمجموعة
        return new PrivateChannel('group.' . $this->group->id);
    }

    public function broadcastWith()
    {
        return [
            'message' => "A request to add files to your group: {$this->group->name} has been made.",
            'files_ids' => $this->files_ids,
        ];
    }
}
