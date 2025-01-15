<?php

namespace App\Repositories;


class UserFacade extends Facade
{
    const aspects_map = [
        'logIn' => ['TransactionAspect', 'LoggingAspect'],
        'register' => ['TransactionAspect', 'LoggingAspect'],
        'logOut' => ['TransactionAspect', 'LoggingAspect'],
        'allUsers' => ['TransactionAspect', 'LoggingAspect'],
        'userNotifications' => ['TransactionAspect','LoggingAspect']
    ];

    public function logIn()
    {
        return $this->userService->logIn($this->message['bodyParameters']);
    }

    public function register()
    {
        return $this->userService->register($this->message['bodyParameters']);
    }

    public function logOut()
    {
        return $this->userService->logOut();
    }

    public function allUsers()
    {
        return $this->userService->allUsers();
    }

    public function userNotifications()
    {
        $userId = $this->message['urlParameters']['userId']; // جلب ID المستخدم من URL
    
        try {
            $notifications = $this->userService->getUserNotifications($userId);
            return $notifications;
        } catch (\Exception $e) {
            if ($e->getCode() === 404) {
                return []; // إرجاع مصفوفة فارغة إذا لم يُعثر على المستخدم
            }
            throw $e; // رمي الاستثناء إذا كانت مشكلة أخرى
        }
    }
    public function myGroups(){
        return $this->groupService->myGroups(auth()->user()->id);
    }

    
}
