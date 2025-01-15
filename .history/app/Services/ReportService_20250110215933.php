<?php

namespace App\Services;

use App\Exceptions\ObjectNotFoundException;
use App\Models\FileLog;
use App\Models\Group;
use Illuminate\Support\Facades\Auth;

class ReportService extends Service
{
    /**
     * تصدير سجل العمليات على مستوى الملفات.
     * متاح لجميع أعضاء المجموعة.
     */
    public function exportFileOperationsReport($filters)
    {
        $query = FileLog::query();

        // تطبيق الفلاتر على التقرير
        if (isset($filters['operation'])) {
            $query->where('operation', $filters['operation']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (isset($filters['date_from']) && isset($filters['date_to'])) {
            $query->whereBetween('date', [$filters['date_from'], $filters['date_to']]);
        }

        return $query->get();
    }

    /**
     * تصدير سجل العمليات على مستوى الأعضاء.
     * متاح فقط لمنشئ المجموعة.
     */
    public function exportUserOperationsReport($message)
    {
        $group = $this->validateGroupCreator($message['urlParameters']['groupId']);

        // استعلام العمليات التي قام بها الأعضاء على الملفات في هذه المجموعة
        $logQuery = FileLog::whereHas('groupFiles', function ($query) use ($group) {
            $query->where('group_id', $group->id);
        });

        $logQuery = $this->applyFilters($logQuery, $message['queryParameters'], ['status', 'user_id']);

        // تجميع السجل حسب المستخدم لعرض نشاط كل مستخدم
        $logsByUser = $logQuery->get()->groupBy('user_id');

        return $logsByUser;
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

    /**
     * تطبيق الفلاتر الديناميكية على الاستعلام.
     */
    private function applyFilters($query, $filters, $allowedFilters)
    {
        foreach ($allowedFilters as $filter) {
            if (isset($filters[$filter])) {
                $query->where($filter, $filters[$filter]);
            }
        }

        return $query;
    }
}
