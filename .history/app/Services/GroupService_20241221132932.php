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


 
    
    public function sendGroupInvitation($groupId, $user_id)
    {
        $group = Group::find($groupId);
        if (!$group) {
            throw new \Exception('Group not found.');
        }
    
        $message = "You have been invited to join the group: {$group->name}";
        
        // بث الإشعار للمستخدم المستهدف
     //   broadcast(new UserNotificationEvent($message, $invitedUserId))->toOthers();
     broadcast(new UserNotificationEvent("You have been invited to join the group! +"));
      
        return response()->json([
            'message' => 'Invitation sent successfully!',
        ]);
    }
    
}
