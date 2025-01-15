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
    public function ExportFileOperationsReport($groupId)
    {
        return $this->reportService->exportFileOperationsReport($this->message, $groupId);
    }

    /**
     * @throws ObjectNotFoundException
     */
    public function ExportUserOperationsReport($groupId)
    {
        return $this->reportService->exportUserOperationsReport($this->message, $groupId);
    }
}
