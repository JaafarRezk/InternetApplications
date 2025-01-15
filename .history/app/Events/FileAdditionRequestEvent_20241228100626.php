<?php
namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Group;
use App\Models\User;

class FileAdditionRequestEvent implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public $group;
    public $fileIds;
    public $userId;

    public function __construct(Group $group, $fileIds, User $user)
    {
        $this->group = $group;
        $this->fileIds = $fileIds;
        $this->userId = $user->id;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('private-user-' . $this->userId);
    }

    public function broadcastAs()
    {
        return 'FileAdditionRequestEvent';
    }

    public function broadcastWith()
    {
        return [
            'message' => "A request to add files to your group: {$this->group->name} has been made.",
            'file_ids' => $this->fileIds,
        ];
    }
}
