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



// routes/channels.php
Broadcast::channel('private-user-{userId}', function ($user, $userId) {
  //return (int) $user->id === (int) $userId;
  return !is_null()true;
});



