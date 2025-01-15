<?php

namespace App\Aspects;

use App\Models\FileLog;

class FileLoggingAspect extends Aspect
{
    public function __construct($message)
    {
        parent::__construct($message);
    }

    public function before()
    {
        FileLog::create([
            "date" => now(),
            "operation" => $this->message["function"],
            "file_id" => null,
            "user_id" => optional(auth()->user())->id,
            "status" => "Started",
        ]);
    }

    public function after()
    {
        if (in_array($this->message["function"], ["checkIn", "checkOut"])) {
            $this->logSingleFileAction("Finished");
        } elseif (in_array($this->message["function"], ["bulkCheckIn", "uploadFiles"])) {
            $this->logBulkFileActions("Finished");
        }
    }

    public function exception()
    {
        if (in_array($this->message["function"], ["checkIn", "checkOut"])) {
            $this->logSingleFileAction("Exception");
        } elseif (in_array($this->message["function"], ["bulkCheckIn", "uploadFiles"])) {
            $this->logBulkFileActions("Exception");
        }
    }

    protected function logSingleFileAction($status)
    {
        FileLog::create([
            "date" => now(),
            "operation" => $this->message["function"],
            "file_id" => $this->getFileId(),
            "user_id" => optional(auth()->user())->id,
            "status" => $status,
        ]);
    }

    protected function logBulkFileActions($status)
    {
        if (!isset($this->message["response"]["data"])) {
            FileLog::create([
                "date" => now(),
                "operation" => $this->message["function"],
                "file_id" => null,
                "user_id" => optional(auth()->user())->id,
                "status" => $status,
            ]);
        } else {
            foreach ($this->message["response"]["data"] as $key => $data) {
                FileLog::create([
                    "date" => now(),
                    "operation" => $this->message["function"],
                    "file_id" => $this->getResponseFileId($key),
                    "user_id" => optional(auth()->user())->id,
                    "status" => $status,
                ]);
            }
        }
    }

    protected function getFileId()
    {
        $id = $this->message["urlParameters"]["id"] ?? $this->message["bodyParameters"]["id"] ?? null;
        return $id;
    }

    protected function getResponseFileId($key)
    {
        return $this->message["response"]["data"][$key]["id"] ?? null;
    }
}
