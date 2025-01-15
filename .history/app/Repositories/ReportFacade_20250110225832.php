<?php

namespace App\Repositories;

use App\Services\ReportService;
use JasonGuru\LaravelMakeRepository\Repository\BaseRepository;

class ReportFacade extends Facade
{
    const aspects_map = array(
        'exportFileOperationsReport' => array('TransactionAspect', 'LoggingAspect'),
        'exportUserOperationsReport' => array('TransactionAspect', 'LoggingAspect'),
    );

    protected $reportService;

    public function __construct($message)
    {
        parent::__construct($message);
        $this->reportService = new ReportService();
    }

    /**
     * تصدير تقرير العمليات على مستوى الملفات
     */
    public function exportFileOperationsReport()
    {
        // الحصول على groupId من الرسالة
        $groupId = $this->message['urlParameters']['groupId'];

        // استدعاء خدمة التقرير
        return $this->reportService->exportFileOperationsReport($groupId);
    }

    /**
     * تصدير تقرير العمليات على مستوى الأعضاء
     */
    public function exportUserOperationsReport()
    {
        // الحصول على groupId من الرسالة
        $groupId = $this->message['urlParameters']['groupId'];

        // استدعاء خدمة التقرير
        return $this->reportService->exportUserOperationsReport($groupId);
    }
}
