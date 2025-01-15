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
Broadcast::channel('private-notifications.{user_id}', function ($user, $user_id) {
  // تحقق مما إذا كان المستخدم الذي يقوم بالاتصال هو نفسه صاحب الإشعار
  return (int) $user->id === (int) $user_id;
});
