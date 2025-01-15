<?php

namespace App\Services;

use App\Exceptions\CreateObjectException;
use App\Exceptions\loginError;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class UserService extends Service{

    /**
     * @throws loginError
     */
    public function logIn($bodyParameters): array
    {
        $data = Validator::make($bodyParameters, [
            'email' => 'required|email',
            'password' => 'required|string'
        ])->validated();
    
        $user = User::where('email', $data['email'])->first();
    
        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw new loginError("User doesn't exist or credentials are wrong");
        }
    
        $roles = $user->getRoleNames()->toArray(); 
        $token = $user->createToken('apiToken')->plainTextToken;
    
        return [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $roles, 
            ],
            'token' => $token
        ];
    }
    
       public function logOut(): bool
    {
        auth()->user()->tokens()->delete();
        return true;
    }

    
    public function register($bodyParameters)
    {
        $parameters = [
            'name' => $bodyParameters['name'],
            'email' => $bodyParameters['email'],
            'password' => bcrypt($bodyParameters['password']) 
        ];
    
        $attempts = 5; 
        while ($attempts > 0) {
            try {
                return DB::transaction(function () use ($parameters) {
                    if (User::where('email', $parameters['email'])->exists()) {
                        throw new \Exception('Email already exists');
                    }
    
                    $user = User::createUserWithDefaultPermissionsAndRole($parameters);
    

                    $roles = $user->roles->pluck('name')->toArray(); 
    
                    $token = $user->createToken('apiToken')->plainTextToken;
    
                    return [
                        'user' => [
                            'id' => $user->id,
                            'name' => $user->name,
                            'email' => $user->email,
                            'roles' => $roles, 
                        ],
                        'token' => $token
                    ];
                    
                });
            } catch (\Exception $e) {
                // التعامل مع حالة الـ deadlock
                if ($e->getCode() === '40001') {
                    $attempts--;
                    sleep(1); 
                } else {
                    throw $e;
                }
            }
        }
    
        throw new \Exception('Unable to register after multiple attempts due to deadlock.');
    }
    
    

    public function allUsers()
    {
        return User::all();
    }


    public function getUserNotifications($userId)
    {
        // التحقق من وجود المستخدم
        $user = User::fetchByIdWithCacheAndAuth($userId);
        if (!$user) {
            throw new \Exception("User not found", 404);
        }
    
        // جلب الإشعارات
        $notifications = Notification::where('user_id', $userId)
            ->orderBy('created_at', 'desc') // ترتيب الإشعارات
            ->get();
    
        return $notifications->toArray(); // تحويل الإشعارات إلى مصفوفة
    }
    
}

