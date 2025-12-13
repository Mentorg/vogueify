<?php

namespace App\Policies;

use App\Enums\RoleName;
use App\Models\User;

class CouponPolicy
{
    public function viewAll(User $user)
    {
        return $user->hasRole(RoleName::ADMIN) || $user->hasRole(RoleName::STAFF);
    }

    public function view(User $user)
    {
        return $user->hasRole(RoleName::ADMIN) || $user->hasRole(RoleName::STAFF);
    }

    public function create(User $user)
    {
        return $user->hasRole(RoleName::ADMIN) || $user->hasRole(RoleName::STAFF);
    }

    public function update(User $user)
    {
        return $user->hasRole(RoleName::ADMIN);
    }

    public function delete(User $user)
    {
        return $user->hasRole(RoleName::ADMIN);
    }

    public function updateStatus(User $user)
    {
        return $user->hasRole(RoleName::ADMIN) || $user->hasRole(RoleName::STAFF);
    }

    public function sendUserNotifications(User $user)
    {
        return $user->hasRole(RoleName::ADMIN) || $user->hasRole(RoleName::STAFF);
    }
}
