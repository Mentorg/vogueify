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

    public function view(User $user)
    {
        return $user->hasRole(RoleName::ADMIN);
    }

    public function modify(User $user)
    {
        return $user->hasRole(RoleName::ADMIN);
    }

    public function delete(User $user, User $target): bool
    {
        return $user->hasRole(RoleName::ADMIN)
            && ! $target->hasRole(RoleName::ADMIN)
            && $user->isNot($target);
    }

    public function restore(User $user): bool
    {
        return $user->hasRole(RoleName::ADMIN);
    }

    public function forceDelete(User $user, User $target): bool
    {
        return $user->hasRole(RoleName::ADMIN)
            && ! $target->hasRole(RoleName::ADMIN)
            && $user->isNot($target);
    }
}
