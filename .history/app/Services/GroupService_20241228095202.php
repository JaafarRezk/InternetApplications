<?php

namespace App\Services;

use App\Models\File;
use App\Models\Group;
use App\Exceptions\CreateObjectException;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\Notification;
use App\Events\UserInvitationEvent;
use Exception;

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
            $group = Group::fetchByIdWithCacheAndAuth($parameters['group_id']);
    
            $invitedUser = User::find($parameters['invited_user_id']);
    
            Notification::createNewWithValidation([
                'user_id' => $parameters['invited_user_id'],
                'group_id' => $parameters['group_id'],  // تضمين group_id هنا
                'message' => "You have been invited to join the group: {$group->name}",
                'time' => now(),
            ]);
    
            event(new UserInvitationEvent($group, $invitedUser));
    
            return ['Invitation sent successfully'];
        } catch (\Exception $e) {
            \Log::error('Error sending invitation', ['message' => $e->getMessage()]);
            throw $e;
        }
    }
    

public function respondToInvitation($parameters)
{
    try {
        $user = auth()->user();

        $group = Group::fetchByIdWithCacheAndAuth($parameters['group_id']);

        if ($parameters['response'] === 'accept') {
            $group->users()->syncWithoutDetaching([$user->id]);
            return ['message' => 'Invitation accepted and user added to the group.'];
        } elseif ($parameters['response'] === 'reject') {
            return ['message' => 'Invitation rejected.'];
        } else {
            throw new \Exception('Invalid response value.');
        }
    } catch (\Exception $e) {
        \Log::error('Error responding to invitation', ['message' => $e->getMessage()]);
        throw $e;
    }
}



public function sendFileAdditionRequest($parameters)
{
    try {
        // جلب المجموعة باستخدام group_id
        $group = Group::fetchByIdWithCacheAndAuth($parameters['group_id']);
        
        // جلب منشئ المجموعة
        $creator = User::find($group->creator_id);

        if (!$creator) {
            throw new Exception('Group creator not found.');
        }

        // إرسال الـ Event عبر Pusher
        broadcast(new FileAdditionRequestEvent($group, $parameters['files_ids'], $creator));

        // إنشاء إشعار في قاعدة البيانات (اختياري إذا كنت بحاجة إلى إشعارات أخرى)
        Notification::createNewWithValidation([
            'user_id' => $group->creator_id, // منشئ المجموعة
            'message' => "A request to add files to your group: {$group->name} has been made.",
            'data' => json_encode(['files_ids' => $parameters['files_ids']]), // تخزين معرفات الملفات
            'time' => now(),
        ]);

        return ['Request to add files sent successfully.'];
    } catch (\Exception $e) {
        \Log::error('Error sending file addition request', ['message' => $e->getMessage()]);
        throw $e;
    }
}



public function searchUsers($query)
{
    try {
        if (!auth()->check()) {
            throw new \Exception("Unauthorized access");
        }
        $users = User::where('name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->limit(10) 
            ->get(['id', 'name', 'email']);

        return $users;
    } catch (\Exception $e) {
        \Log::error('Error searching users', ['message' => $e->getMessage()]);
        throw $e;
    }

}

public function groupDetails($groupId)
{
    // الحصول على المجموعة باستخدام المعرف
    $group = Group::find($groupId);

    if (!$group) {
        throw new Exception('Group not found or access denied.');
    }

    // جلب الملفات مع تحديد الحقول بوضوح لتجنب الغموض
    $files = $group->files()->get(['files.id as file_id', 'files.name', 'files.mime_type', 'files.size']);

    // جلب المستخدمين مع تحديد الحقول بوضوح
    $users = $group->users()->get(['users.id as user_id', 'users.name', 'users.email']);

    // إعداد النتيجة النهائية
    return [
        'group' => [
            'id' => $group->id,
            'name' => $group->name,
            'creator_id' => $group->creator_id,
        ],
        'files' => $files,
        'users' => $users,
    ];
}


public function removeFilesFromGroup($bodyParameters){
    $group = Group::fetchByIdWithCacheAndAuth($bodyParameters['group_id']);
    $ids = $bodyParameters['files_ids'][0];
    $ids_arr = preg_split ("/\,/", $ids);

    if($this->checkFilesOwnership($ids_arr)){
        foreach($ids_arr as $id){
            $group->files()->detach($id);
        }
        return true;
    }else{
        return null;
    }
}


public function removeUsersFromGroup($bodyParameters,$groupFiles){
    $group = Group::fetchByIdWithCacheAndAuth($bodyParameters['group_id']);

    $ids = $bodyParameters['users_ids'][0];
    $ids_arr = preg_split ("/\,/", $ids);

    if(!$this->checkUsersCheckedFilesInGroup($groupFiles,$ids_arr) && !in_array(auth()->user()->id,$ids_arr)){
        foreach($ids_arr as $id){
            $group->users()->detach($id);
        }
        return true;
    }else{
        return false;
    }

}


public function checkUsersCheckedFilesInGroup($groupFiles,$ids_arr){

    if(!empty($groupFiles)){
        foreach($groupFiles as $file){
            if(in_array($file->file_holder_id,$ids_arr)){
                return true;
            }
        }
    }else{
        return false;
    }

}
public function getGroupFiles($id){
    $group = Group::fetchByIdWithCacheAndAuth($id);
    return $group->files()->get();
}

}



