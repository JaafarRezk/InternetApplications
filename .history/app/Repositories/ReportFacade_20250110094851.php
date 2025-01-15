<?php

namespace App\Repositories;

use App\Exceptions\ObjectNotFoundException;

class LogFacade extends Facade
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
        return $this->logService->exportOperationsReport($this->message);
    }

    public function FileReports()
    {
        return $this->logService->exportFileReport($this->message);
    }

    public function UserFileReports(){
        return $this->logService->exportUserFileReports($this->message);
    }
}
