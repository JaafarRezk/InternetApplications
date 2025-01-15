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

    });
});






/*


<?php

namespace App\Services;

use App\Exceptions\CheckInException;
use App\Exceptions\FileDeletionException;
use App\Exceptions\FileInUseException;
use App\Exceptions\MaxFileSizeException;
use App\Exceptions\MaxNumFileException;
use App\Models\File;
use App\Exceptions\ObjectNotFoundException;
use Illuminate\Support\Facades\DB;

use Exception;
use Illuminate\Support\Facades\Storage;
use App\Exceptions\FileNotCheckedOutException;
use Illuminate\Support\Facades\Gate;

class FileService extends Service
{
    public function uploadFiles($bodyParameters)
    {
        if (count($bodyParameters) > 0 && count($bodyParameters['files']) > 0) {
            $files = $bodyParameters['files'];
            $returnFiles = [];

            foreach ($files as $key => $file) {
                $storagePath = Storage::disk('public')->put('documents', $file);

                $maxFileNumPerUser = env('MAX_FILE_NUM_PER_USER');
                if ($maxFileNumPerUser !== null && $maxFileNumPerUser !== '') {
                    $countFiles = File::where('user_id', auth()->user()->id)->count();
                    if ($countFiles + 1 > $maxFileNumPerUser) {
                        throw new MaxNumFileException('User Uploaded Maximum Number of Files !!');
                    }
                }

                $maxFileSizePerUserMB = env('MAX_FILE_SIZE_PER_USER');
                if ($maxFileSizePerUserMB !== null && $maxFileSizePerUserMB !== '') {
                    $maxFileSizeBytes = $maxFileSizePerUserMB * 1024 * 1024;
                    if ($file->getSize() > $maxFileSizeBytes) {
                        throw new MaxFileSizeException('File Size Is Larger Than ' . $maxFileSizePerUserMB . 'MB !!');
                    }
                }

                $fileData = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $storagePath,
                    'mime_type' => $file->extension(),
                    'size' => $file->getSize(),
                    'checked' => 0,
                    'version_number' => 1,
                    'is_current' => true,
                    'user_id' => auth()->user()->id,
                    'file_holder_id' => null,
                ];

                $newFile = File::create($fileData);
                $returnFiles[$key] = $newFile;
            }
            return $returnFiles;
        } else {
            return null;
        }
    }


    public function checkIn($id)
    {
        // بدء المعاملة باستخدام TransactionAspect
        DB::beginTransaction();

        try {
            // قفل الصف في قاعدة البيانات لضمان عدم التحديث المتزامن
            $file = File::where('id', $id)->lockForUpdate()->first(); // قفل الصف حتى يتم التحديث

            if (!$file) {
                throw new ObjectNotFoundException('File not found');
            }

            if ($file->checked == 0) {  // تحقق من أن الملف غير محجوز
                $latestVersion = $file->versions()->orderBy('version_number', 'desc')->first();
                $file->versions()->create([
                    'file_id' => $file->id,
                    'name' => $file->name,
                    'path' => $file->path,
                    'mime_type' => $file->mime_type,
                    'size' => $file->size,
                    'version_number' => $latestVersion ? $latestVersion->version_number + 1 : 1,
                ]);

                $file->update([
                    'checked' => 1,
                    'file_holder_id' => auth()->user()->id,
                ]);

                // تأكيد التحديثات في المعاملة
                DB::commit();
                return $file->refresh();
            } else {
                throw new FileInUseException('File is already checked out by another user.');
            }
        } catch (\Exception $e) {
            // في حالة حدوث خطأ، التراجع عن المعاملة
            DB::rollBack();
            throw $e;  // إعادة رمي الاستثناء
        }
    }

public function checkOut($bodyParameters)
{
    $id = $bodyParameters["id"];
    $file = File::fetchByIdWithCacheAndAuth($id);

    if (!$file) {
        throw new ObjectNotFoundException('File not found');
    }

    if ($file->checked == 1) {
        $newFile = $bodyParameters['file'];

        $originalFileName = basename($file->path);
        $newFilePath = 'documents/' . $originalFileName;
        Storage::disk('public')->put($newFilePath, file_get_contents($newFile));

        $latestVersion = $file->versions()->orderBy('version_number', 'desc')->first();

        $newVersion = $file->versions()->create([
            'file_id' => $file->id,
            'name' => $file->name,
            'path' => $newFilePath,
            'mime_type' => $newFile->getClientMimeType(),
            'size' => $newFile->getSize(),
            'version_number' => $latestVersion ? $latestVersion->version_number + 1 : 1,
        ]);

        $file->update([
            'checked' => 0,
            'path' => $newFilePath,
            'file_holder_id' => auth()->user()->id,
            'name' ,
            'size' => $newVersion->size,
        ]);

        return $file->refresh();
    } else {
        throw new FileNotCheckedOutException('File is not checked out or does not exist.');
    }
}


public function checkInMultipleFiles(array $fileIds)
{
    $files = [];
    
    // استخدام قفل الصفوف لضمان أن المستخدمين الآخرين لا يمكنهم تعديل الملفات في نفس الوقت
    DB::beginTransaction();
    
    try {
        // جلب الملفات مع قفل الصفوف
        foreach ($fileIds as $fileId) {
            $file = File::where('id', $fileId)->lockForUpdate()->first();
            if (!$file) {
                throw new ObjectNotFoundException("File with ID {$fileId} not found.");
            }
            $files[] = $file;
        }

        // التحقق من أن جميع الملفات غير محجوزة
        foreach ($files as $file) {
            if ($file->checked != 0) {
                throw new FileInUseException("File ID {$file->id} is in use and cannot be checked in.");
            }
        }

        // الآن نقوم بإنشاء النسخة الجديدة للملفات وتحديث حالتها
        $updatedFiles = [];
        foreach ($files as $file) {
            $latestVersion = $file->versions()->orderBy('created_at', 'desc')->first();
            $file->versions()->create([
                'file_id' => $file->id,
                'name' => $file->name,
                'path' => $file->path,
                'mime_type' => $file->mime_type,
                'size' => $file->size,
                'version_number' => $latestVersion ? $latestVersion->version_number + 1 : 1,
            ]);

            $file->updateWithConditions([
                'checked' => 1,
                'file_holder_id' => auth()->user()->id,
            ], [
                'id' => $file->id,
            ]);

            $updatedFiles[] = $file->refresh();
        }

        // تأكيد المعاملة
        DB::commit();

        return $updatedFiles;
    } catch (\Exception $e) {
        // التراجع عن المعاملة إذا حدث استثناء
        DB::rollBack();
        throw new CheckInException("Failed to check in all files: " . $e->getMessage());
    }

    }





    public function getMyFiles($perPage = 10)
    {
        return File::where('user_id', auth()->id())->paginate($perPage);
    }

  

public function getAllFiles($perPage = 10)
{
    $filesQuery = File::query();

    $paginatedFiles = $filesQuery->paginate($perPage);

    $authorizedFiles = []; 

    foreach ($paginatedFiles as $file) {
        try {
            $fileInstance = File::find($file->id);

            if (Gate::allows('viewFile', $fileInstance)) {
                $authorizedFiles[] = $fileInstance;
            }
        } catch (\Exception $e) {
            continue;
        }
    }
    return [
        'files' => $authorizedFiles,
        'pagination' => [
            'current_page' => $paginatedFiles->currentPage(),
            'last_page' => $paginatedFiles->lastPage(),
            'total' => $paginatedFiles->total(),
            'per_page' => $paginatedFiles->perPage(),
            'next_page_url' => $paginatedFiles->nextPageUrl(),
            'prev_page_url' => $paginatedFiles->previousPageUrl(),
        ],
    ];
}


public function getFileVersions($fileId)
{
    $file = File::find($fileId);

    if (!$file) {
        throw new ObjectNotFoundException('File not found');
    }
    $versions = $file->versions()->get();

    return $versions;
}

}

*/