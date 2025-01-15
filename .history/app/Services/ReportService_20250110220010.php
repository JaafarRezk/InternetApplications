<?php

namespace App\Services;

use App\Exceptions\ObjectNotFoundException;
use App\Models\FileLog;
use App\Models\Group;
use Illuminate\Support\Facades\Auth;
use App\Models\Log;

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
    public function exportUserOperationsReport($filters)
    {
        $query = Log::query();

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
            $query->whereBetween('created_at', [$filters['date_from'], $filters['date_to']]);
        }

        return $query->get();
    }
}
