<?php

namespace App\Services;

use App\Models\FileLog;
use App\Models\Group;
use Illuminate\Support\Facades\Auth;

class ReportService extends Service
{
    /**
     * تقرير العمليات على مستوى الملفات.
     */
    public function exportFileOperationsReport($groupId)
    {
        // التحقق من أن المستخدم لديه حق الوصول إلى المجموعة
        $group = $this->validateGroupAccess($groupId);

        // جلب السجلات المرتبطة بالمجموعة
        $logs = FileLog::whereHas('file.groups', function ($query) use ($group) {
            $query->where('group_id', $group->id);
        })->get();

        return $logs;
    }

    /**
     * تقرير العمليات على مستوى الأعضاء.
     */
    public function exportUserOperationsReport($groupId)
    {
        $group = $this->validateGroupCreator($groupId);

        $logs = FileLog::whereHas('file.groups', function ($query) use ($group) {
            $query->where('group_id', $group->id);
        })->get()->groupBy('user_id');

        return $logs;
    }

   
    private function validateGroupAccess($groupId)
    {
        $group = Group::findOrFail($groupId);

        if (!$group->users()->wherePivot('user_id', Auth::id())->exists()) {
            throw new \Exception("Unauthorized access to group file operations report.");
        }

        return $group;
    }


    private function validateGroupCreator($groupId)
    {
        $group = Group::findOrFail($groupId);

        if ($group->creator_id !== Auth::id()) {
            throw new \Exception("Unauthorized access to user operations report.");
        }

        return $group;
    }
}
