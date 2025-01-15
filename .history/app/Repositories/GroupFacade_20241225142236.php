<?php
namespace App\Repositories;

class GroupFacade extends Facade
{
    CONST aspects_map = array(
        'createGroup' => array('TransactionAspect'),
        'removeGroup'=> array('TransactionAspect'),
        'addFilesToGroup'=> array('TransactionAspect'),
        'addUsersToGroup'=> array('TransactionAspect'),
        'removeFilesFromGroup'=> array('TransactionAspect'),
        'removeUsersFromGroup'=> array('TransactionAspect'),
        'myGroups'=> array('TransactionAspect'),
        'enrolledGroups'=> array('TransactionAspect'),
        'filesInGroup'=> array('TransactionAspect'),
        'sendGroupInvitation' => array('TransactionAspect'), // إضافة هنا
        
    );

    public function __construct($message)
    {
        parent::__construct($message);
    }

    public function createGroup()
    {
        $group = $this->groupService->createGroup($this->message['bodyParameters']);
        $message = [
            'group_id' => $group->id,
            'users_ids' => [auth()->user()->id]
        ];
         //$groupAdded = $this->groupService->addUsersToGroup($message);
        return $group;
    }

    public function myGroups(){
        return $this->groupService->myGroups(auth()->user()->id);
    }

    public function addFilesToGroup()
    {
        $res = $this->groupService->addFilesToGroup($this->message['bodyParameters']);
        return $res;
    }
 
    public function sendGroupInvitation()
    {
        // سجل محتويات bodyParameters للتأكد من استقبال البيانات
        \Log::info('Body Parameters:', $this->message['bodyParameters']);
    
        // تحقق من وجود المعطيات المطلوبة
        if (
            !isset($this->message['bodyParameters']['group_id']) ||
            !isset($this->message['bodyParameters']['invited_user_id'])
        ) {
            throw new \Exception('Missing required parameters: group_id or invited_user_id');
        }
    
        $groupId = $this->message['bodyParameters']['group_id'];
        $invitedUserId = $this->message['bodyParameters']['invited_user_id'];
    
        return $this->groupService->sendGroupInvitation($groupId, $invitedUserId);
    }
    
    public function respondToInvitation()
{
    $groupId = $this->message['bodyParameters']['group_id'] ?? null;
    $response = $this->message['bodyParameters']['response'] ?? null;
    return $this->groupService->respondToGroupInvitation($groupId, $response);
}


   
    /*
    public function removeFilesFromGroup()
    {
        $res = $this->groupService->removeFilesFromGroup($this->message['bodyParameters']);
        return $res;
    }
    
    public function removeUsersFromGroup()
    {
        $groupFiles = $this->groupService->getGroupFiles($this->message["bodyParameters"]["group_id"]);
        $res = $this->groupService->removeUsersFromGroup($this->message['bodyParameters'],$groupFiles);
        return $res;
    }

    public function removeGroup()
    {

        $id = $this->message['urlParameters']['id'];
        $files = $this->groupService->getGroupFiles($id);

        if(!empty($files->toArray())){
            $files_ids_imploded= implode(', ', $files->pluck('id')->toArray());
            $check = $this->fileService->bulkCheckIn($files_ids_imploded);
        }

        $res = $this->groupService->removeGroup($id);
        $this->fileService->freeFiles($files);
        return $res??null;
    }
    
   
    
    public function enrolledGroups()
    {
        return $this->groupService->groupsUserEnrolledIn(auth()->user());
    }
    
    public function filesInGroup()
    {
        $id = $this->message['urlParameters']['id'];
        return $this->groupService->getGroupFiles($id);
    }
        */
}
