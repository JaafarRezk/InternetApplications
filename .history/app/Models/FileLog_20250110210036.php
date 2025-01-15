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
        'group_id', // إضافة معرف المجموعة
    ];

    public $timestamps = false;

    /**
     * علاقة مع الملفات.
     */
    public function file()
    {
        return $this->belongsTo(File::class);
    }

    /**
     * علاقة مع المستخدمين.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * علاقة مع المجموعة.
     */
    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
