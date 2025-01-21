<?php

namespace App\Services;

use App\Models\FileLog;
use App\Models\Group;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Log;

class ReportService extends Service
{

    const aspects_map = array(
        'exportFileOperationsReport' => array('TransactionAspect', 'LoggingAspect'),
        'exportUserOperationsReport' => array('TransactionAspect', 'LoggingAspect'),
        'exportFileOperationsReportAsCSV' => array('TransactionAspect', 'LoggingAspect'),
        'exportFileOperationsReportAsPDF' => array('TransactionAspect', 'LoggingAspect'),
    );

    public function exportFileOperationsReport($groupId)
    {
        $group = $this->validateGroupAccess($groupId); 
    
        $logs = FileLog::whereHas('file.groups', function ($query) use ($group) {
            $query->where('group_id', $group->id);
        })
        ->with(['file', 'user']) 
        ->get();
    
        return $logs;
    }
    
    
    public function exportUserOperationsReport($groupId)
    {
        $group = $this->validateGroupCreator($groupId);
    
        $logs = Log::whereHas('user.groups', function ($query) use ($group) {
            $query->where('group_id', $group->id);
        })
        ->with(['user', 'file']) 
        ->get();
        
        return $logs;
    }
    

    

   
    

   
    private function validateGroupAccess($groupId)
    {
        $group = Group::findOrFail($groupId);
    
        if (!$group->users()->wherePivot('user_id', Auth::id())->exists() && $group->creator_id !== Auth::id()) {
            throw new \Exception("Unauthorized access to group file operations report.");
        }
    
        return $group;
    }
    

    private function validateGroupCreator($groupId)
    {
        $group = Group::findOrFail($groupId);
    
        if ($group->creator_id !== Auth::id()) {
            throw new \Exception("Unauthorized access to user operations report.");
        }
    
        return $group;
    }


    public function exportFileOperationsReportAsCSV($groupId)
    {
        $logs = $this->exportFileOperationsReport($groupId);
    
        $data = $logs->map(function ($log) {
            return [
                'Date' => $log->date,
                'Operation' => $log->operation,
                'File ID' => $log->file_id,
                'User ID' => $log->user_id,
                'Status' => $log->status,
                'Group ID' => $log->group_id,
            ];
        });
    
        $fileName = 'file_operations_report_' . now()->timestamp . '.csv';
        $reportsPath = storage_path('app/public/reports');
    
        // إنشاء المجلد إذا لم يكن موجودًا
        if (!is_dir($reportsPath)) {
            mkdir($reportsPath, 0777, true);
        }
    
        // المسار النسبي للملف
        $filePath = 'public/reports/' . $fileName;
    
        // تخزين الملف باستخدام Excel
        Excel::store(new \App\Exports\ArrayExport($data), $filePath);
    
        // إرجاع رابط الملف
        return response()->json([
            'success' => true,
            'message' => 'Report exported successfully.',
            'file_url' => asset('storage/reports/' . $fileName),
        ]);
    }

    
    
    public function exportFileOperationsReportAsPDF($groupId)
    {
        $logs = $this->exportFileOperationsReport($groupId);
    
        $pdf = PDF::loadView('reports.file_operations', ['logs' => $logs]);
    
        $pdf->save(storage_path('app/public/reports/file_operations_report.pdf'));
    
        return response()->json([
            'success' => true,
            'message' => 'Report exported successfully.',
            'file_url' => url('storage/reports/file_operations_report.pdf'),
        ]);
    }
    

}
