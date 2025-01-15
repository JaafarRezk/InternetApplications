<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends GenericModel
{
    use HasFactory;

    public $fillable = [
        'name',
        'creator_id',
    ];

    public $validation_rules = [
        'name'=>'unique:groups,name',
    ];
    public function files()
    {
        return $this->belongsToMany(File::class, 'group_files','group_id')->withTimestamps();
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'group_users','group_id')->withTimestamps();
    }
}
