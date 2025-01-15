<?php

namespace App\Services;

use App\Models\File;
use App\Models\Group;
use App\Exceptions\CreateObjectException;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\Notification;
use App\Events\UserInvitationEvent;

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
    $group = Group::fetchByIdWithCacheAndAuth($bodyParameters['group_id']);

    $ids_arr = $bodyParameters['files_ids'];

    if ($this->checkFilesOwnership($ids_arr)) {
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


 
    public static function sendGroupInvitation($parameters)
{
    try {
        // التحقق من وجود المجموعة باستخدام التخزين المؤقت والصلاحيات
        $group = self::fetchByIdWithCacheAndAuth($parameters['group_id']);

        // التحقق من وجود المستخدم المدعو
        $invitedUser = User::fetchByIdWithCacheAndAuth($parameters['invited_user_id']);

        // إنشاء الإشعار باستخدام createNewWithValidation
        Notification::createNewWithValidation([
            'user_id' => $parameters['invited_user_id'],
            'message' => "You have been invited to join the group: {$group->name}",
            'time' => now(),
        ]);

        // إطلاق الحدث
        event(new UserInvitationEvent($group, $invitedUser));

        return ['message' => 'Invitation sent successfully'];
    } catch (\Exception $e) {
        \Log::error('Error sending invitation', ['message' => $e->getMessage()]);
        throw $e;
    }
}

    

    public function addUsersToGroup($bodyParameters){
        $group = Group::fetchByIdWithCacheAndAuth($bodyParameters['group_id']);
        $ids = $bodyParameters['users_ids'][0];
        $ids_arr = preg_split ("/\,/", $ids);
        $group->users()->syncWithoutDetaching($ids_arr);

        return true;
    }
  
}

