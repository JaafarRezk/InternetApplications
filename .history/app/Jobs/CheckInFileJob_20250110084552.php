<?php

namespace App\Jobs;

use App\Models\File;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckInFileJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $fileId;
    protected $userId;

    public function __construct($fileId, $userId)
    {
        $this->fileId = $fileId;
        $this->userId = $userId;
    }

    public function handle()
    {
        $file = File::find($this->fileId);

        if ($file->checked != 0) {
            throw new \Exception("File is already checked out by another user.");
        }

        $file->update([
            'checked' => 1,
            'file_holder_id' => $this->userId,
        ]);
    }
}
