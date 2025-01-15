<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends GenericModel
{
    use HasFactory;

    public  $fillable=[
      "operation",
      "status",
      "username",
      "user_id"
    ];

    public $timestamps = false;
}
