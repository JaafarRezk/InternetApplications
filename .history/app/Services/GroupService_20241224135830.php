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
        try {
            // جلب المجموعة باستخدام الكاش والتحقق من الصلاحيات
            $group = Group::fetchByIdWithCacheAndAuth($groupId);
            if (!$group) {
                \Log::error('Group not found', ['groupId' => $groupId]);
                throw new \Exception('Group not found.');
            }
    
            // جلب المستخدم المدعو باستخدام الكاش والتحقق من الصلاحيات
            $invitedUser = User::fetchByIdWithCacheAndAuth($invitedUserId);
            if (!$invitedUser) {
                \Log::error('Invited user not found', ['invitedUserId' => $invitedUserId]);
                throw new \Exception('Invited user not found.');
            }
    
            // تسجيل الإشعار في قاعدة البيانات
            Notification::c([
                'user_id' => $invitedUserId,
                'message' => "You have been invited to join the group: {$group->name}",
                'time' => now(),
            ]);
    
            // إطلاق حدث الدعوة
            event(new UserInvitationEvent($group, $invitedUser));
    
            \Log::info('Invitation sent successfully', ['group' => $group, 'invitedUser' => $invitedUser]);
    
            return response()->json(['message' => 'Invitation sent successfully!'], 200);
        } catch (\Exception $e) {
            \Log::error('Error sending invitation', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
    
            return response()->json(['error' => 'Failed to send invitation'], 500);
        }
    }
    
    
    
}

