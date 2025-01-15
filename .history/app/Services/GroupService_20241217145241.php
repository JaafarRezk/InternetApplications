<?php

namespace App\Services;

use App\Models\File;
use App\Models\Group;
use App\Exceptions\CreateObjectException;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Events\GroupInvitationEvent;

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
        // تحقق من وجود المجموعة
        $group = Group::find($groupId);
        if (!$group) {
            return response()->json(['error' => 'Group not found.'], 404);
        }
    
        // تحقق من المستخدم المدعو
        $invitedUser = User::find($invitedUserId);
        if (!$invitedUser) {
            return response()->json(['error' => 'User not found.'], 404);
        }
    
        // إنشاء نص الرسالة
        $message = "You have been invited to join the group: {$group->name}";
    
        // بث الإشعار باستخدام الحدث
        broadcast(new UserNotificationEvent($message, $invitedUserId))->toOthers();
    
        return response()->json(['message' => 'Invitation sent successfully!'], 200);
    }
    
}
