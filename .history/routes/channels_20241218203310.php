<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// routes/channels.php

Broadcast::channel('private-notifications.{userId}', function ($user, $userId) {
    \Log::info('Authenticating user for channel', ['user_id' => $user->id, 'channel_user_id' => $userId]);
    return (int) $user->id === (int) $userId; // تحقق من أن المستخدم هو الذي يحق له تلقي الإشعار
});
