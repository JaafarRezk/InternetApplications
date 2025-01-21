<?php

namespace App\Aspects;

use App\Models\Log;

class LoggingAspect extends Aspect
{


    public function __construct($message)
    {
        parent::__construct($message);
    }

    public function before(){
        if(in_array('Started', config('log'))){
            Log::create([
                "operation"=>$this->message["function"],
                "username"=>auth()->user() != null ? auth()->user()->name : "",
                "status"=>"Started",
                "user_id"=>auth()->user() != null ? auth()->user()->id : null,
            ]);
        }
    }
    public function after(){
        if(in_array('Finished', config('log'))){
            Log::create([
                "operation"=>$this->message["function"],
                "username"=>auth()->user() != null ? auth()->user()->name : "",
                "status"=>"Finished",
                "user_id"=>auth()->user() != null ? auth()->user()->id : null,
            ]);
        }
    }
    public function exception(){
        if(in_array('Exception', config('log'))){
            Log::create([
                "operation"=>$this->message["function"],
                "username"=>auth()->user() != null ? auth()->user()->name : "",
                "status"=>"Exception",
                "user_id"=>auth()->user() != null ? auth()->user()->id : null,
            ]);
        }
    }

}
