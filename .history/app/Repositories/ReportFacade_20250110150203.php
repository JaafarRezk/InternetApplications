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
        $id = $this->message['urlParameters']['id'];

        $report = $this->reportService->exportFileOperationsReport($groupId);
        return $report
    }
    
    public function checkIn()
    {
        $id = $this->message['urlParameters']['id'];
        $file = $this->fileService->checkIn($id);
        return $file;
    }

    /**
     * @throws ObjectNotFoundException
     */
    public function ExportUserOperationsReport()
    {
       
        $id = $this->message['urlParameters']['id'];
        return $this->reportService->exportUserOperationsReport($id);
    }
}
