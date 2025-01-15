<?php

namespace App\Policies;

use App\Models\File;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class FilePolicy
{
    public function readFile(User $user, $obj): bool
    {
        if($obj instanceof File)
            return $user->groups->intersect($obj->groups)->isNotEmpty() || $obj->user_id == auth()->user()->id;
        else
            return true;
    }
    public function checkIn(User $user, $obj): bool
    {
        if($obj instanceof File)
            return $user->groups->intersect($obj->groups)->isNotEmpty() || $obj->user_id == auth()->user()->id;
        else
            return true;
    }

    public function removeFiles(User $user, $obj): bool
    {
        if($obj instanceof File)
            return $user->id == $obj->user_id;
        else
            return true;
    }

    public function checkOut(User $user, $obj): bool
    {
        if($obj instanceof File)
            return $user->id == $obj->file_holder_id;
        else
            return true;
    }

    public function viewFile(User $user, File $file): bool
    {
        return $file->groups->isEmpty() || $user->groups->intersect($file->groups)->isNotEmpty();
    }

  

    public function downloadFile(User $user, File $file): bool
    {
        // السماح لمالك الملف بتنزيله دائمًا
        if ($file->user_id === $user->id) {
            return true;
        }
    
        // السماح للمستخدم العادي بتنزيل الملف فقط إذا كان بحالة Checked-In
        return $file->file_holder_id === $user->id && $file->checked == 0;
    }
    

}
