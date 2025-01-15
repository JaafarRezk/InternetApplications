<?php

namespace App\Repositories;

use App\Services\ReportService;
use JasonGuru\LaravelMakeRepository\Repository\BaseRepository;

class ReportFacade extends Facade
{
    const aspects_map = array(
        'exportFileOperationsReport' => array('TransactionAspect', 'LoggingAspect'),
        'exportUserOperationsReport' => array('TransactionAspect', 'LoggingAspect'),
        'exportFileOperationsReportAsCSV' => array('TransactionAspect', 'LoggingAspect'),
        'exportFileOperationsReportAsPDF' => array('TransactionAspect', 'LoggingAspect'),
    );

    protected $reportService;

    public function __construct($message)
    {
        parent::__construct($message);
        $this->reportService = new ReportService();
    }

    public function exportFileOperationsReport()
    {
        $groupId = $this->message['urlParameters']['groupId'];

        return $this->reportService->exportFileOperationsReport($groupId);
    }

    public function exportUserOperationsReport()
    {
        $groupId = $this->message['urlParameters']['groupId'];

        return $this->reportService->exportUserOperationsReport($groupId);
    }

    public function exportFileOperationsReportAsCSV()
    {
        $groupId = $this->message['urlParameters']['groupId'];
        return $this->reportService->exportFileOperationsReportAsCSV($groupId);
    }

    /**
     * تصدير تقرير العمليات على مستوى الملفات كـ PDF.
     */
    public function exportFileOperationsReportAsPDF()
    {
        $groupId = $this->message['urlParameters']['groupId'];
        return $this->reportService->exportFileOperationsReportAsPDF($groupId);
    }
}
