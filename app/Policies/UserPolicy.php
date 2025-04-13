<?php

namespace App\Policies;

use App\Enums\RoleName;
use App\Models\User;

class UserPolicy
{
    public function viewAll(User $user)
    {
        return $user->hasRole(RoleName::ADMIN);
    }

    public function modify(User $user)
    {
        return $user->hasRole(RoleName::ADMIN);
    }
}
