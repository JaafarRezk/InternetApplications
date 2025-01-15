<?php

namespace App\Repositories;


class UserFacade extends Facade
{
    const aspects_map = [
        'logIn' => ['TransactionAspect', 'LoggingAspect'],
        'register' => ['TransactionAspect', 'LoggingAspect'],
        'logOut' => ['TransactionAspect', 'LoggingAspect'],
        'allUsers' => ['TransactionAspect', 'LoggingAspect'],
        'notify' => ['TransactionAspect','LoggingAspect']
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

    public function userNotifications($userId)
    {
        try {
            $notifications = $this->userService->getUserNotifications($userId);
    
            return $notifications; // إرجاع البيانات إذا كانت موجودة
        } catch (\Exception $e) {
            if ($e->getCode() === 404) {
                return [];
            }
            throw $e;
        }
    }
    
}
