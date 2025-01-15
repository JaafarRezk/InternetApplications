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
Broadcast::cha('private-notifications.{userId}', function ($user, $userId) {
    Log::info("Authenticating user for private channel", [
        'authenticated_user_id' => $user->id,
        'requested_user_id' => $userId,
    ]);

    if ((int) $user->id === (int) $userId) {
        Log::info("Access granted for user: {$user->id}");
        return true;
    }

    Log::error("Unauthorized access attempt for user: {$user->id}");
    return false;
});