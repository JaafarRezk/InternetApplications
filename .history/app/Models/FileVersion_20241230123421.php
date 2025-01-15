<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileVersion extends Ge
{
    use HasFactory;

    protected $fillable = [
        'file_id',
        'name',
        'path',
        'mime_type',
        'size',
        'version_number',
    ];

    public function file()
    {
        return $this->belongsTo(File::class);
    }
}
