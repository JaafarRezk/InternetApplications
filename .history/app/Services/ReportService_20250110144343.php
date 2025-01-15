<?php

namespace App\Services;

use App\Exceptions\ObjectNotFoundException;
use App\Models\FileLog;
use App\Models\Log;
use App\Models\Group;
use Illuminate\Support\Facades\Auth;

class ReportService extends Service
{
    /**
     * Export file-level operations report.
     * Accessible to all group members.
     *
     * @throws ObjectNotFoundException
     */
    public function exportFileOperationsReport($groupId)
    {
        $group = Group::findOrFail($groupId);

        // Ensure the authenticated user is a member of the group
        if (!$group->users()->where('user_id', Auth::id())->exists()) {
            throw new \Exception("Unauthorized access to group file operations report.");
        }

        $log = FileLog::where('group_id', $groupId);
        if (isset($message['queryParameters']['status'])) {
            $log = $log->where('status', $message['queryParameters']['status']);
        }

        if (isset($message['queryParameters']['file_id'])) {
            $log = $log->where('file_id', $message['queryParameters']['file_id']);
        }

        return $log->get();
    }

    /**
     * Export user-level operations report.
     * Accessible only to the group creator.
     *
     * @throws ObjectNotFoundException
     */
    public function exportUserOperationsReport($groupId)
    {
        $group = Group::findOrFail($groupId);

        // Ensure the authenticated user is the creator of the group
        if ($group->creator_id !== Auth::id()) {
            throw new \Exception("Unauthorized access to user operations report.");
        }

        $log = Log::whereHas('user', function ($query) use ($groupId) {
            $query->whereHas('groups', function ($q) use ($groupId) {
                $q->where('group_id', $groupId);
            });
        });

        if (isset($message['queryParameters']['status'])) {
            $log = $log->where('status', $message['queryParameters']['status']);
        }

        if (isset($message['queryParameters']['user_id'])) {
            $log = $log->where('user_id', $message['queryParameters']['user_id']);
        }

        return $log->get();
    }
}
