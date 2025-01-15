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


// قناة طلب إضافة الملفات الخاصة
Broadcast::channel('private-user-{userId}-file-request', function ($user, $userId) {
  \Log::info('Broadcasting file addition request auth attempt', [
      'user_id' => $user->id,
      'requestedUserId' => $userId,
  ]);

  return (int) $user->id === (int) $userId;
});




