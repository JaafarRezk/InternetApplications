<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransformerController;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use App\Events\UserNotificationEvent;


Route::get('/redis-test', function () {
    Cache::put('test_key', 'Hello Redis!', 600);
    return Cache::get('test_key');
});


Route::controller(TransformerController::class)->group(function () {

    Route::post('/auth/logIn', 'transform')->name('user.logIn');
    Route::post('/auth/register', 'transform')->name('user.register')->middleware('throttle:api');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/auth/logOut', 'transform')->name('user.logOut');
        Route::get('/allUsers',  'transform')->name('user.allUsers');
        Route::post('file/uploadFiles', 'transform')->name('file.uploadFiles');
        Route::post('/createGroup', 'transform')->name('group.createGroup');
        Route::get('/MyGroups', 'transform')->name('group.MyGroups');
        Route::post('/addFilesToGroup', 'transform')->name('group.addFilesToGroup');
        Route::post('/sendGroupInvitation', 'transform')->name('group.sendGroupInvitation');
        Route::get('file/getMyFiles', 'transform')->name('file.getMyFiles')->middleware('throttle:api');
        Route::get('file/getAllFiles', 'transform')->name('file.getAllFiles')->middleware('throttle:api');
        Route::post('file/checkIn/{id}',  'transform')->name('file.checkIn');
        Route::post('file/checkOut',  'transform')->name('file.checkOut');
        Route::post('file/checkInMultipleFiles',  'transform')->name('file.checkInMultipleFiles');
    });
});



Route::middleware('auth:sanctum')->post('/send-notification', function (Request $request) {
    $senderId = auth()->user()->id; // المستخدم الذي أرسل الإشعار
    $receiverId = $request->input('receiver_id'); // المستخدم المستهدف بالإشعار
    $message = $request->input('message', "Hello User $receiverId, you have a notification from User $senderId!");

    if (!$receiverId) {
        return response()->json(['error' => 'Receiver ID is required.'], 400);
    }

    // تخزين الإشعار في جلسة المستخدم المستهدف
    session()->push("notifications.user_$receiverId", $message);

    // بث الإشعار
    broadcast(new \App\Events\UserNotificationEvent($message, $receiverId));

    return response()->json(['message' => 'Notification sent successfully.']);
});


Route::middleware('auth:sanctum')->get('/notifications', function () {
    $userId = auth()->user()->id;
    $notifications = session("notifications.user_$userId", []);
    return response()->json(['notifications' => $notifications]);
});


    // إضافة Route لاسترجاع الإشعارات
    Route::middleware('auth:sanctum')->post('/notifications/clear', function () {
        $userId = auth()->user()->id;
        session()->forget("notifications.user_$userId");
        return response()->json(['message' => 'Notifications cleared.']);
    });
    