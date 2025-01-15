<?php

namespace App\Repositories;

use App\Exceptions\ObjectNotFoundException;

class ReportFacade extends Facade
{
    const aspects_map = array(
        'ExportFileOperationsReport' => array('TransactionAspect', 'LoggingAspect'),
        'ExportUserOperationsReport' => array('TransactionAspect', 'LoggingAspect'),
    );

    public function __construct($message)
    {
        parent::__construct($message);
    }

    /**
     * @throws ObjectNotFoundException
     */
    public function ExportFileOperationsReport()
    {
        if (!isset($this->message['urlParameters']['id'])) {
            return $this->exceptionResponse("The 'id' parameter is missing.", 400);
        }
    
        $groupId = $this->message['urlParameters']['id'];
        $report = $this->reportService->exportFileOperationsReport($this->message, $groupId);
        return $report;
    }
    
    public function ExportUserOperationsReport()
    {
        if (!isset($this->message['urlParameters']['id'])) {
            return $this->exceptionResponse("The 'id' parameter is missing.", 400);
        }
    
        $groupId = $this->message['urlParameters']['id'];
        $report = $this->reportService->exportUserOperationsReport($this->message, $groupId);
        return $report;
    }
    
}
