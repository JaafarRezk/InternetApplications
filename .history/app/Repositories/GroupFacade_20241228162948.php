<?php
namespace App\Repositories;

use Arr;

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
        'sendGroupInvitation' => array('TransactionAspect'), 
        'sendFileAdditionRequest'=> array('TransactionAspect'),
        'respondToFileAdditionRequest' =>array('TransactionAspect'),
        'respondToInvitation' =>array('TransactionAspect'),
        'removeFilesFromGroup'=>array('TransactionAspect'),
        'groupDetails' => array('TransactionAspect'),
        'removeUsersFromGroup'=>array('TransactionAspect'),
        'enrolledGroups'=>array('TransactionAspect'),

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


    public function sendFileAdditionRequest()
{
    // سجل المعاملات التي تم استلامها في السجلات
    \Log::info('Body Parameters:', $this->message['bodyParameters']);
    
    // التحقق من وجود المعاملات المطلوبة
    if (
        !isset($this->message['bodyParameters']['group_id']) ||
        !isset($this->message['bodyParameters']['files_ids'])
    ) {
        throw new \Exception('Missing required parameters: group_id or files_ids');
    }

    // استخراج المعاملات المطلوبة من bodyParameters
    $parameters = [
        'group_id' => $this->message['bodyParameters']['group_id'],
        'files_ids' => $this->message['bodyParameters']['files_ids'],
    ];

    // تمرير المعاملات إلى خدمة groupService لاستدعاء التابع sendFileAdditionRequest
    return $this->groupService->sendFileAdditionRequest($parameters);
}


public function respondToFileAdditionRequest()
{
    // سجل المعاملات التي تم استلامها في السجلات
    \Log::info('Body Parameters:', $this->message['bodyParameters']);
    
    // التحقق من وجود المعاملات المطلوبة
    if (
        !isset($this->message['bodyParameters']['group_id']) ||
        !isset($this->message['bodyParameters']['files_ids']) ||
        !isset($this->message['bodyParameters']['response'])
    ) {
        throw new \Exception('Missing required parameters: group_id, files_ids, or response');
    }

    // استخراج المعاملات المطلوبة من bodyParameters
    $parameters = [
        'group_id' => $this->message['bodyParameters']['group_id'],
        'files_ids' => $this->message['bodyParameters']['files_ids'],
        'response' => $this->message['bodyParameters']['response'], // القبول أو الرفض
        'requester_user_id' => $this->message['bodyParameters']['requester_user_id'], // معرف المستخدم الذي طلب إضافة الملفات
    ];

    // تمرير المعاملات إلى خدمة groupService لاستدعاء التابع respondToFileAdditionRequest
    return $this->groupService->respondToFileAdditionRequest($parameters);
}

 
    public function sendGroupInvitation()
    {
        // سجل المعاملات التي تم استلامها في السجلات
        \Log::info('Body Parameters:', $this->message['bodyParameters']);
        
        // التحقق من وجود المعاملات المطلوبة
        if (
            !isset($this->message['bodyParameters']['group_id']) ||
            !isset($this->message['bodyParameters']['invited_user_id'])
        ) {
            throw new \Exception('Missing required parameters: group_id or invited_user_id');
        }
    
        // استخراج المعاملات المطلوبة من bodyParameters
        $parameters = [
            'group_id' => $this->message['bodyParameters']['group_id'],
            'invited_user_id' => $this->message['bodyParameters']['invited_user_id']
        ];
    
        // تمرير المعاملات إلى خدمة groupService لاستدعاء التابع sendGroupInvitation
        return $this->groupService->sendGroupInvitation($parameters);
    }
    
    public function respondToInvitation()
    {
        \Log::info('Body Parameters:', $this->message['bodyParameters']);
        
        if (
            !isset($this->message['bodyParameters']['group_id']) ||
            !isset($this->message['bodyParameters']['response'])
        ) {
            throw new \Exception('Missing required parameters: group_id or response');
        }
    
        $parameters = [
            'group_id' => $this->message['bodyParameters']['group_id'],
            'response' => $this->message['bodyParameters']['response']
        ];
            return $this->groupService->respondToInvitation($parameters);
    }
    
    public function searchUsers()
{
    \Log::info('Search Query:', $this->message['queryParameters']);

    if (!isset($this->message['queryParameters']['query'])) {
        throw new \Exception('Missing required query parameter: query');
    }

    $query = $this->message['queryParameters']['query'];
    return $this->groupService->searchUsers($query);
}

public function groupDetails()
{
    if (!isset($this->message['bodyParameters']['group_id'])) {
        throw new \Exception('Missing required parameter: group_id');
    }

    $groupId = $this->message['bodyParameters']['group_id'];
    return $this->groupService->groupDetails($groupId);
}
   


public function enrolledGroups()
{
    // سجل المعاملات التي تم استلامها في السجلات (اختياري)
    \Log::info('User enrolled groups requested by user ID:', [auth()->user()->id]);

    // استدعاء التابع من GroupService لعرض المجموعات التي انضم إليها المستخدم
    return $this->groupService->enrolledGroups(auth()->user()->id);
}


public function removeFilesFromGroup()
{
    $res = $this->groupService->removeFilesFromGroup($this->message['bodyParameters']);
    return $res;
} 

public function removeUsersFromGroup(){
    $groupFiles = $this->groupService->getGroupFiles($this->message["bodyParameters"]["group_id"]);
    $res = $this->groupService->removeUsersFromGroup($this->message['bodyParameters'],$groupFiles);
    return $res;
}

}