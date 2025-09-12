<?php

namespace App\Policies;

use App\Enums\RoleName;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OrderItemPolicy
{
    public function modify(User $user, OrderItem $orderItem)
    {
        return $user->hasRole(RoleName::ADMIN) || $user->hasRole(RoleName::STAFF);
    }
}
