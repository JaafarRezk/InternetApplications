<?php

namespace App\Services;

use App\Exceptions\CreateObjectException;
use App\Exceptions\loginError;
use App\Models\Notification;
use App\Models\User;
use App\Models\Group;
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


    public function getUserNotifications($id)
    {
        return Notification::where('user_id', $id)->get();
    }
  

    public function respondToGroupInvitation($groupId, $response)
{
    try {
        // جلب المجموعة
        $group = Group::find($groupId);
        if (!$group) {
            \Log::error('Group not found', ['groupId' => $groupId]);
            throw new \Exception('Group not found.');
        }

        // التحقق من إذا كان المستخدم مدعوًا
        $userId = auth()->user()->id;
        $notification = Notification::where('user_id', $userId)
            ->where('message', "You have been invited to join the group: {$group->name}")
            ->first();

        if (!$notification) {
            \Log::error('Invitation notification not found', ['userId' => $userId, 'groupId' => $groupId]);
            throw new \Exception('Invitation notification not found.');
        }

        if ($response === 'accept') {
            // قبول الدعوة: إضافة المستخدم إلى المجموعة
            $group->users()->attach($userId);
            \Log::info('User accepted invitation', ['userId' => $userId, 'groupId' => $groupId]);
            $notification->delete(); // حذف الإشعار بعد قبول الدعوة
            return response()->json(['message' => 'Invitation accepted successfully!'], 200);
        } elseif ($response === 'reject') {
            // رفض الدعوة: فقط حذف الإشعار
            $notification->delete();
            \Log::info('User rejected invitation', ['userId' => $userId, 'groupId' => $groupId]);
            return response()->json(['message' => 'Invitation rejected successfully!'], 200);
        } else {
            \Log::error('Invalid response to invitation', ['response' => $response]);
            throw new \Exception('Invalid response.');
        }
    } catch (\Exception $e) {
        \Log::error('Error responding to invitation', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);
        return response()->json(['error' => 'Failed to respond to invitation'], 500);
    }
}

}

