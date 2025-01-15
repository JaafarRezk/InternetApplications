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


Broadcast::channel('user.{userId}', function ($user, $userId) {
    // تحقق من أن المستخدم الحالي هو نفسه المستخدم الذي يمتلك المعرف (id)
    return (int) $user->id === (int) $userId;

}


Broadcast::channel('notifications', function ($user) {
    return true; // أو تحقق من المستخدم إذا لزم الأمر
});

