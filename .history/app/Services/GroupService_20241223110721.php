<?php

namespace App\Services;

use App\Models\File;
use App\Models\Group;
use App\Exceptions\CreateObjectException;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Events\GroupInvitationEvent;
use App\Events\UserNotificationEvent;

class GroupService extends Service{
  

    public function createGroup($bodyParameters)
    {
        $parameters = [
            'name' => $bodyParameters['name'],
            'creator_id' => auth()->user()->id,
        ];
        $group = Group::createNewWithValidation($parameters);
        return $group;
    }


    public function myGroups($id){
        return Group::where('creator_id', $id)->get();
    }

    

    public function addFilesToGroup($bodyParameters)
{
    // جلب المجموعة
    $group = Group::fetchByIdWithCacheAndAuth($bodyParameters['group_id']);

    // افتراض أن 'files_ids' مصفوفة مباشرة
    $ids_arr = $bodyParameters['files_ids'];

    // تحقق من ملكية الملفات قبل الإضافة
    if ($this->checkFilesOwnership($ids_arr)) {
        // إضافة الملفات إلى المجموعة بدون إزالة الملفات السابقة
        $group->files()->syncWithoutDetaching($ids_arr);
        return response()->json(['message' => 'Files added successfully!'], 200);
    } else {
        return response()->json(['error' => 'You do not own all the files.'], 403);
    }
}

    public function checkFilesOwnership($ids_arr)
    {
        foreach($ids_arr as $id){
            $file = File::fetchByIdWithCacheAndAuth($id);
            if($file->user_id != auth()->user()->id){
                return false;
            }
        }
        return true;
    }


 
    public function sendGroupInvitation($groupId, $invitedUserId)
    {
        $group = Group::find($groupId);
    
        if (!$group) {
            throw new \Exception('Group not found.');
        }
    
        $invitedUser = User::find($invitedUserId);
    
        if (!$invitedUser) {
            throw new \Exception('Invited user not found.');
        }
    
        // تمرير كائنات Group و User مباشرة
        event(new GroupInvitationEvent($group, $invitedUser));
    
        return response()->json(['message' => 'Invitation sent successfully!'], 200);
    }
    
}


/*
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

*/