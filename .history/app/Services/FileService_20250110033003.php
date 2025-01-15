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
                    'file_holder_id' => auth()->user()->id,
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
    $file = File::fetchByIdWithCacheAndAuth($id);

    if (!$file) {
        throw new ObjectNotFoundException('File not found');
    }

    if ($file->checked == 0) {
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

        return $file->refresh();
    } else {
        throw new FileInUseException('File is already checked out by another user.');
    }
}



public function checkOut($bodyParameters)
{
    $id = $bodyParameters["id"];
    $file = File::fetchByIdWithCacheAndAuth($id);

    if (!$file) {
        throw new ObjectNotFoundException('File not found');
    }

    // تحقق من أن الملف في حالة محجوزة (Checked-Out)
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
        foreach ($fileIds as $fileId) {
            $files[] = File::fetchByIdWithCacheAndAuth($fileId);
        }
        foreach ($files as $file) {
            if ($file->checked != 0) {
                throw new FileInUseException("File ID {$file->id} is in use and cannot be checked in.");
            }
        }

        try {
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

            return $updatedFiles;
        } catch (\Exception $e) {
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
            $fileInstance = File::fetchByIdWithCacheAndAuth($file->id);

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

