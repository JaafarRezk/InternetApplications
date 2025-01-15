<?php

namespace App\Repositories;

use App\Services\FileService;
use App\Services\ReportService;
use App\Services\UserService;
use App\Services\GroupService;
use JasonGuru\LaravelMakeRepository\Repository\BaseRepository;


//use Your Model

/**
 * Class Facade.
 */
class Facade extends BaseRepository
{

    protected $message;

    private $facadeMapper = [];
    protected $userService;
    protected $fileService;
    protected $groupService;
    protected $reportService;
    protected $aspects_map = [];

    public function __construct($message)
    {
        $this->userService = new UserService();
        $this->groupService = new GroupService();
        $this->fileService = new FileService();
        $this->reportService = new ReportService();

        $this->facadeMapper = [
            "user" => "App\\Repositories\\UserFacade",
            "group" => "App\\Repositories\\GroupFacade",
            "file" => "App\\Repositories\\FileFacade",
            "report"=> "App\\Repositories\\ReportFacade",
        ];

        $this->message = $message;
    }

    /**
     * @return string
     *  Return the model
     */
    public function model()
    {
        return \App\Models\GenericModel::class;
    }

    public function response($instance, $successMessage, $errorMessage)
    {
        return $this->message['response'] =
            [
                "success" => (is_array($instance)&& empty($instance) ? true : ($instance != null)) ,
                "data" => $instance ?? null,
                "message" => $instance != null ? $successMessage : $errorMessage,
            ];

    }

    public function exceptionResponse($errorMessage,$statusCode)
    {

        return $this->message['response'] =
            [
                "success" => false,
                "message" => $errorMessage,
                "statusCode" => $statusCode,
            ];

    }

    public function execute()
    {
        $facade = $this->message["facade"];
        $func = $this->message["function"];
        $facadeClass = new $this->facadeMapper[$facade]($this->message);
        try {

            $this->executeBefore($func, $facadeClass);

            $result = $facadeClass->$func();
            $this->message["response"] = $this->response($result, 
            __("api." . $facade . "." . $func . ".success"), 
            __("api." . $facade . "." . $func . ".failure"));

            $this->executeAfter($func, $facadeClass);

            return $this->message;
        } catch (\Exception $e) {
            $this->executeException($func, $facadeClass);
            $this->message["response"] = $this->exceptionResponse($e->getMessage(),500);
            return $this->message;
        }
    }

    public function executeBefore($func, $facadeClass)
    {
        $aspects = $facadeClass::aspects_map[$func] ?? null;
        if ($aspects) {
            foreach ($aspects as $aspect) {
                $obj = "App\\Aspects\\" . $aspect;
                $class = new $obj($this->message);
                $class->before();
            }
        }
    }

    public function executeAfter($func, $facadeClass)
    {
        $aspects = $facadeClass::aspects_map[$func] ?? null;
        if ($aspects) {
            foreach ($aspects as $aspect) {
                $obj = "App\\Aspects\\" . $aspect;
                $class = new $obj($this->message);
                $class->after();
            }
        }
    }

    public function executeException($func, $facadeClass)
    {
        $aspects = $facadeClass::aspects_map[$func] ?? null;
        if ($aspects) {
            foreach ($aspects as $aspect) {
                $obj = "App\\Aspects\\" . $aspect;
                $class = new $obj($this->message);
                $class->exception();
            }
        }
    }
}
