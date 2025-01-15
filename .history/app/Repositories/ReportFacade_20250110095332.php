<?php

namespace App\Repositories;

use App\Exceptions\ObjectNotFoundException;

class ReportFacade extends Facade
{
    const aspects_map = array(
        'ExportOperationsReport' => array('TransactionAspect', 'LoggingAspect'),
        'FileReports' => array('TransactionAspect', 'LoggingAspect'),
    );

    public function __construct($message)
    {
        parent::__construct($message);
    }

    /**
     * @throws ObjectNotFoundException
     */
    public function ExportOperationsReport()
    {
        return $this->reportService->exportOperationsReport($this->message);
    }

    public function FileReports()
    {
        return $this->reportService->exportFileReport($this->message);
    }

    public function UserFileReports(){
        return $this->reportService->exportUserFileReports($this->message);
    }
}
