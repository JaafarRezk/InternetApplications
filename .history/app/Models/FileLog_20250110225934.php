<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'operation',
        'file_id',
        'user_id',
        'status',
        'group_id',
    ];

    
    public function file()
    {
        return $this->belongsTo(File::class, 'file_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * العلاقة مع المجموعة.
     */
    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    /**
     * وظيفة لتسجيل العمليات على الملفات.
     *
     * @param string $operation
     * @param int $fileId
     * @param int|null $userId
     * @param string $status
     * @param int|null $groupId
     * @return void
     */
    public static function logAction($operation, $fileId, $userId = null, $status = 'Started', $groupId = null)
    {
        self::create([
            'date' => now(),
            'operation' => $operation,
            'file_id' => $fileId,
            'user_id' => $userId ?? auth()->id(),
            'status' => $status,
            'group_id' => $groupId,
        ]);
    }
}
