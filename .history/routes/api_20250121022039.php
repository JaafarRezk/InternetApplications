<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransformerController;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use App\Events\UserNotificationEvent;
use App\Http\Controllers\NotificationController;

Broadcast::routes(['middleware' => ['auth:api']]);

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
        Route::get('file/getMyFiles', 'transform')->name('file.getMyFiles')->middleware('throttle:api');
        Route::get('file/getAllFiles', 'transform')->name('file.getAllFiles')->middleware('throttle:api');
        
        Route::get('file/{id}/versions','transform')->name('file.getFileVersions');

        Route::post('file/checkIn/{id}',  'transform')->name('file.checkIn');
        Route::post('file/checkOut',  'transform')->name('file.checkOut');
        Route::post('file/checkInMultipleFiles',  'transform')->name('file.checkInMultipleFiles');
        Route::post('/send-invitation', 'transform')->name('group.sendGroupInvitation');
        Route::post('/send-File-AdditionRequest', 'transform')->name('group.sendFileAdditionRequest');
        Route::get('/notifications',  'transform')->name('user.userNotifications');
        Route::post('/respond-to-invitation', 'transform')->name('group.respondToInvitation');
        Route::post('/respond-ToFileAddition-Request', 'transform')->name('group.respondToFileAdditionRequest');
        Route::get('/search-users', 'transform')->name('group.searchUsers');
        Route::post('/group/details', 'transform')->name('group.groupDetails');
        Route::post('/removeFilesFromGroup','transform')->name('group.removeFilesFromGroup');
        Route::post('/removeUsersFromGroup','transform')->name('group.removeUsersFromGroup');
        Route::get('/enrolledGroups','transform')->name('group.enrolledGroups');
        Route::get('/downloadFile/{id}','downloadFile')->name('file.downloadFile');
        Route::get('/reports/file-operations/{groupId}', 'transform')->name('report.exportFileOperationsReport');
        Route::get('/reports/file-operations/csv/{groupId}', 'transform')->name('report.exportFileOperationsReportAsCSV');
        Route::get('/reports/file-operations/pdf/{groupId}', 'transform')->name('report.exportFileOperationsReportAsCSV');

        Route::middleware('role:admin')->group(function () {
            Route::get('reports/user-operations/{groupId}', 'transform')->name('report.exportUserOperationsReport');

        });
        
        
    });



 
});




