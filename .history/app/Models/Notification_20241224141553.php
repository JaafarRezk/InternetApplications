<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends GenericModel
{
    use HasFactory;

    protected $fillable = [
        'user_id',', 'message', 'time',
    ];
}
