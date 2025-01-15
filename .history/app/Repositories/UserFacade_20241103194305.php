<?php

namespace App\Repositories;


class UserFacade extends Facade
{
    const aspects_map = [
        'logIn' => ['TransactionAspect', 'LoggingAspect'],
        'register' => ['TransactionAspect', 'LoggingAspect'],
        'logOut' => ['TransactionAspect', 'LoggingAspect'],
        'allUsers' => ['TransactionAspect', 'LoggingAspect'],
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
}
