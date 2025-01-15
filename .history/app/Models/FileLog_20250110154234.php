<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileLog extends Model
{
    use HasFactory;

    public  $fillable=[
        "date",
        "operation",
        "file_id",
        "user_id",
        "status"
    ];
    public $timestamps = false;

}
