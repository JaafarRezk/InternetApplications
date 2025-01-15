<?php
namespace App\Models;

use App\Exceptions\CreateObjectException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use App\Jobs\AssignRoleAndPermissionsJob;
use App\Models\Notification;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $guard = 'web';

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    private $defaultRole = 'User';
    private $userPermissions = [
        'user-view-dashboard',
    ];

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_users', 'user_id')->withTimestamps();
    }

    public function getUserNotifications($userId)
    {
        // استخدام GenericModel للبحث عن المستخدم
        $user = User::fetchByIdWithCacheAndAuth($userId);

        // جلب الإشعارات
        $notifications = Notification::where('user_id', $userId)
            ->orderBy('created_at', 'desc') // ترتيب الإشعارات
            ->get();

        return $notifications->toArray(); // تحويل الإشعارات إلى مصفوفة
    }

    public static function createUserWithDefaultPermissionsAndRole(array $parameters, string $role = null, array $permissions = [])
    {
        $class_name = get_called_class();
        $class = new $class_name();
    
        // Validate parameters before entering the transaction
        $validationRules = [
            'name' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string',
        ];
    
        $validator = Validator::make($parameters, $validationRules);
    
        if ($validator->fails()) {
            throw new CreateObjectException($validator->errors()->first());
        }
    
        // Start transaction
        return DB::transaction(function () use ($class, $parameters, $role, $permissions) {
            // Create user
            $user = $class::create($parameters);
    
            // Dispatch job to assign role and permissions
            AssignRoleAndPermissionsJob::dispatch($user, $role ?? $class->defaultRole, !empty($permissions) ? $permissions : $class->userPermissions);
    
            return $user;
        });
    }
}
