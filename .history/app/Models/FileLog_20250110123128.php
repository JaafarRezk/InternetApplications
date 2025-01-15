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
        'group_id',
        'status'
    ];

    public $timestamps = false;

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}