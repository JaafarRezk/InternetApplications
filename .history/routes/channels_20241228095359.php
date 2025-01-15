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
Broadcast::channel('private-user-{invitedUserId}', function ($user, $invitedUserId) {
  \Log::info('Broadcasting auth attempt', [
      'user_id' => $user->id,
      'invitedUserId' => $invitedUserId,
  ]);


  return (int) $user->id === (int) $invitedUserId;
});





