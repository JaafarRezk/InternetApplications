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
        if (!$groupId) {
            throw new \Exception("Group ID is required.");
        }
        
        return $this->reportService->exportFileOperationsReport($this->message, $groupId);
    }
    

    /**
     * @throws ObjectNotFoundException
     */
    public function ExportUserOperationsReport()
    {
        $groupId = $this->message['queryParameters']['group_id'] ?? null;
        if (!$groupId) {
            throw new \InvalidArgumentException("Group ID is required.");
        }
        $id = $this->message['urlParameters']['id'];
        return $this->reportService->exportUserOperationsReport($this->message, $groupId);
    }
}
