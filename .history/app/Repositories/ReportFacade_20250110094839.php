<?php

namespace App\Services;

use App\Exceptions\ObjectNotFoundException;
use App\Models\FileLog;
use App\Models\Log;

class LogService extends Service
{

    /**
     * @throws ObjectNotFoundException
     */
    public function exportOperationsReport($message){
        $log = Log::where('id','!=','');

        if(isset($message["queryParameters"]["status"])){
            $log=$log->where('status',$message["queryParameters"]["status"]);
        }

        if(isset($message["queryParameters"]["user_id"])){
            $log=$log->where('user_id',$message["queryParameters"]["user_id"]);
        }

       return $log->get();
    }

    public function exportFileReport($message){
        $log = FileLog::where('id','!=','');

        if(isset($message["queryParameters"]["status"])){
            $log=$log->where('status',$message["queryParameters"]["status"]);
        }

        if(isset($message["queryParameters"]["file_id"])){
            $log=$log->where('file_id',$message["queryParameters"]["file_id"]);
        }

        if(isset($message["queryParameters"]["user_id"])){
            $log=$log->where('user_id',$message["queryParameters"]["user_id"]);
        }

        return $log->get();
    }

    public function exportUserFileReports($message){
        $log = FileLog::where('id','!=','');
        $id = auth()->user()->id;

        if($id != ""){
            $log=$log->where('user_id',$id);
        }else{
            return null;
        }

        return $log->get();
    }
}
