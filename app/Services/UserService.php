<?php

namespace App\Services;

use App\Exceptions\CreateObjectException;
use App\Exceptions\loginError;
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

    $attempts = 5; // عدد المحاولات
    while ($attempts > 0) {
        try {
            return DB::transaction(function () use ($parameters) {
                // تأكد من عدم وجود بيانات مستخدم مكررة
                if (User::where('email', $parameters['email'])->exists()) {
                    throw new \Exception('Email already exists');
                }

                return User::createUserWithDefaultPermissionsAndRole($parameters);
            });
        } catch (\Exception $e) {
            // تحقق من حدوث deadlock
            if ($e->getCode() === '40001') {
                $attempts--;
                sleep(1); // الانتظار قبل إعادة المحاولة
            } else {
                throw $e; // أعادة طرح الاستثناء لأي خطأ آخر
            }
        }
    }

    throw new \Exception('Unable to register after multiple attempts due to deadlock.');
}

    
    
    

    public function allUsers()
    {
        return User::all();
    }
}

