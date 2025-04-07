<?php

namespace App\Policies;

use App\Enums\RoleName;
use App\Models\User;

class ProductPolicy
{
    public function modify(User $user)
    {
        return $user->hasRole(RoleName::ADMIN) || $user->hasRole(RoleName::STAFF);
    }
}
