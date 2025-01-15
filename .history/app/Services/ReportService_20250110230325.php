<?php

namespace App\Services;

use App\Models\FileLog;
use App\Models\Group;
use Illuminate\Support\Facades\Auth;

class ReportService extends Service
{

    public function exportFileOperationsReport($groupId)
    {
        $group = $this->validateGroupAccess($groupId);

        $logs = FileLog::whereHas('file.groups', function ($query) use ($group) {
            $query->where('group_id', $group->id);
        })->get();

        return $logs;
    }

    public function exportUserOperationsReport($groupId)
    {
        $group = $this->validateGroupCreator($groupId);

        $logs = FileLog::whereHas('file.groups', function ($query) use ($group) {
            $query->where('group_id', $group->id);
        })->get()->groupBy('user_id');

        return $logs;
    }

   
    private function validateGroupAccess($groupId)
    {
        $group = Group::findOrFail($groupId);

        if (!$group->users()->wherePivot('user_id', Auth::id())->exists()) {
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

        $fileName = 'file_operations_report.csv';

        return Excel::download(new \App\Exports\ArrayExport($data), $fileName);
    }

    public function exportFileOperationsReportAsPDF($groupId)
    {
        $logs = $this->exportFileOperationsReport($groupId);

        $pdf = Pdf::loadView('reports.file_operations', ['logs' => $logs]);

        return $pdf->download('file_operations_report.pdf');
    }

}
