<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $path
 * @property string $mime_type
 * @property int $size
 * @property int $checked
 * @property int|null $user_id
 * @property int|null $file_holder_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Group> $groups
 * @property-read int|null $groups_count
 * @property-read \App\Models\User|null $holder
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FileVersion> $versions
 * @property-read int|null $versions_count
 * @method static \Illuminate\Database\Eloquent\Builder|File newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|File newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|File query()
 * @method static \Illuminate\Database\Eloquent\Builder|File whereChecked($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereFileHolderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereMimeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereUserId($value)
 */
	class File extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string|null $date
 * @property string $operation
 * @property string|null $status
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property int|null $user_id
 * @property int|null $file_id
 * @method static \Illuminate\Database\Eloquent\Builder|FileLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FileLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FileLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|FileLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FileLog whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FileLog whereFileId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FileLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FileLog whereOperation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FileLog whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FileLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FileLog whereUserId($value)
 */
	class FileLog extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $file_id
 * @property string $name
 * @property string $path
 * @property string $mime_type
 * @property int $size
 * @property int $version_number
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\File $file
 * @method static \Illuminate\Database\Eloquent\Builder|FileVersion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FileVersion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FileVersion query()
 * @method static \Illuminate\Database\Eloquent\Builder|FileVersion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FileVersion whereFileId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FileVersion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FileVersion whereMimeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FileVersion whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FileVersion wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FileVersion whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FileVersion whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FileVersion whereVersionNumber($value)
 */
	class FileVersion extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @method static \Illuminate\Database\Eloquent\Builder|GenericModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GenericModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GenericModel query()
 */
	class GenericModel extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $creator_id
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\File> $files
 * @property-read int|null $files_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|Group newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Group newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Group query()
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereCreatorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereUpdatedAt($value)
 */
	class Group extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $operation
 * @property string $username
 * @property string $status
 * @property string $created_at
 * @property int|null $user_id
 * @method static \Illuminate\Database\Eloquent\Builder|Log newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Log newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Log query()
 * @method static \Illuminate\Database\Eloquent\Builder|Log whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Log whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Log whereOperation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Log whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Log whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Log whereUsername($value)
 */
	class Log extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property mixed $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Group> $groups
 * @property-read int|null $groups_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|User withoutRole($roles, $guard = null)
 */
	class User extends \Eloquent {}
}

