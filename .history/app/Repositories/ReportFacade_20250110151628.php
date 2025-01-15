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

    public function __construct($message)
    {
        parent::__construct($message);
        $this->service = new ReportService();
    }

    public function exportFileOperationsReport()
    {
        return $this->service->exportFileOperationsReport($this->message);
    }

    public function exportUserOperationsReport()
    {
        return $this->service->exportUserOperationsReport($this->message);
    }
}