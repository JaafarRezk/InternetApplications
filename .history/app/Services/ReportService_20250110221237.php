<?php

namespace App\Services;

use App\Models\FileLog;
use App\Models\Log;
use Illuminate\Support\Facades\Auth;

class ReportService extends Service
{
    /**
     * تقرير العمليات على مستوى الملفات.
     */
    public function exportFileOperationsReport($groupId)
    {
        $group = $this->validateGroupAccess($groupId);

        $logs = FileLog::where('group_id', $group->id)->get();

        return $logs;
    }

    /**
     * تقرير العمليات على مستوى الأعضاء.
     */
    public function exportUserOperationsReport($groupId)
    {
        $group = $this->validateGroupCreator($groupId);

        $logs = FileLog::whereHas('groupFiles', function ($query) use ($group) {
            $query->where('group_id', $group->id);
        })->get()->groupBy('user_id');

        return $logs;
    }

    /**
     * التحقق من وصول المستخدم إلى المجموعة.
     */
    private function validateGroupAccess($groupId)
    {
        $group = Group::findOrFail($groupId);

        if (!$group->users()->wherePivot('user_id', Auth::id())->exists()) {
            throw new \Exception("Unauthorized access to group file operations report.");
        }

        return $group;
    }

    /**
     * التحقق من أن المستخدم هو منشئ المجموعة.
     */
    private function validateGroupCreator($groupId)
    {
        $group = Group::findOrFail($groupId);

        if ($group->creator_id !== Auth::id()) {
            throw new \Exception("Unauthorized access to user operations report.");
        }

        return $group;
    }
}
