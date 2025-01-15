<?php

namespace App\Broadcasting;

use App\Models\User;

use Illuminate\Support\Facades\Gate;
use Illuminate\Broadcasting\Channel;

class InvitationsChannel
{
    public function join($user)
    {
        return true; // يمكن تعديل هذه القاعدة حسب الصلاحيات
    }
}