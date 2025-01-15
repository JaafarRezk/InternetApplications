<?php

namespace App\Policies;

use App\Models\Group;
use App\Models\User;
use App\Models\File;

class GroupPolicy
{
  

    public function removeUsersFromGroup(User $user, $obj): bool
    {
        if($obj instanceof Group)
            return $user->id == $obj->creator_id;
        else
            return true;
    }

    public function addUsersToGroup(User $user, $obj): bool
    {
        if($obj instanceof Group)
            return $user->id == $obj->creator_id;
        else
            return true;
    }

    public function removeGroup(User $user, $obj): bool
    {
        if($obj instanceof Group)
            return $user->id == $obj->creator_id;
        else
            return true;
    }
    
    public function viewFileInGroup(User $user, File $file): bool
    {
        $group = $file->group; 
        if ($group) {
            return $group->users->contains($user->id);
        }

        return true;
    }

}
