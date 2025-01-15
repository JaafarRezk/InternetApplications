<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Repositories\Facade;
use Illuminate\Support\Facades\Cache;
use App\Events\NewNotification;
use App\Models\File;

class TransformerController extends Controller
{
    public function transform(Request $request)
    {

        

        $currentRoute = Route::current();
        $routeName = $currentRoute->getName();
        $method = $request->method();

        $message = $this->getRouteExploded([], $routeName);
        $message['method'] = $method;

        $message = array_merge($message, $this->getParameters($currentRoute, $request));

        $facade = new Facade($message);
        $result = $facade->execute();

        return response()->json($result['response'], $result['response']['statusCode'] ?? 200);
    }

    public function getRouteExploded(array $message, string $routeName): array
    {
        $exp_arr = explode(".", $routeName);
        if (count($exp_arr) === 2) {
            $message["facade"] = $exp_arr[0];
            $message["function"] = $exp_arr[1];
        }
        return $message;
    }

    private function getParameters($currentRoute, Request $request): array
    {
        return [
            'urlParameters' => $currentRoute->parameters() ?? [],
            'queryParameters' => $request->query() ?? [],
            'bodyParameters' => $request->isMethod('POST') ? $request->all() : []
        ];
    }


 
    public function downloadFile($id)
    {
        $file = File::find($id);
    
        if (auth()->user()->cannot('downloadFile', $file)) {
            abort(403, 'You must check in the file to download it, unless you are the owner.');
        }
    
        $path = storage_path('app/public/' . $file->path);
    
        // التحقق من وجود الملف فعليًا في المسار
        if (!file_exists($path)) {
            abort(404, 'File not found.');
        }
    
        // إرسال الملف للتحميل
        return response()->file($path);
    }
    
    

    
}


