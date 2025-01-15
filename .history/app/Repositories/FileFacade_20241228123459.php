<?php

namespace App\Repositories;

use App\Services\FileService;
use JasonGuru\LaravelMakeRepository\Repository\BaseRepository;

class FileFacade extends Facade
{
    const aspects_map = array(
        'getMyFiles' => array('TransactionAspect', 'LoggingAspect'),
        'checkIn' => array('TransactionAspect', 'LoggingAspect','FileLoggingAspect'),
        'checkInMultipleFiles' => array('TransactionAspect', 'LoggingAspect','FileLoggingAspect'),
        'checkOut' => array('TransactionAspect', 'LoggingAspect','FileLoggingAspect'),
        'uploadFiles' => array('TransactionAspect', 'LoggingAspect','FileLoggingAspect'),
        'getAllFiles' =>array('TransactionAspect', 'LoggingAspect','FileLoggingAspect'),
       'downloadFile' =>array('TransactionAspect', 'LoggingAspect','FileLoggingAspect'),
    );

    public function __construct($message)
    {
        parent::__construct($message);
    }

    public function uploadFiles()
    {
        $files = $this->fileService->uploadFiles($this->message['bodyParameters']);
        return $files;
    }


    public function checkIn()
    {
        $id = $this->message['urlParameters']['id'];
        $file = $this->fileService->checkIn($id);
        return $file;
    }

    public function checkOut()
    {

        $bodyParameters = $this->message['bodyParameters'];
        $file = $this->fileService->checkOut($bodyParameters);
        return $file;
    }

    public function checkInMultipleFiles()
    {
        $fileIds = $this->message['bodyParameters']['fileIds'];
        $files = $this->fileService->checkInMultipleFiles($fileIds);
        return $files;
    }

    public function getMyFiles()
    {
        $files = $this->fileService->getMyFiles();
        return $files;
    }

    public function getAllFiles()
{
    $files = $this->fileService->getAllFiles();     
    return $files;
}


}
