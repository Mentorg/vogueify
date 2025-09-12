<?php

namespace App\Policies;

use App\Enums\RoleName;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;

class OrderPolicy
{
    public function viewAll(User $user)
    {
        return $user->hasRole(RoleName::ADMIN) || $user->hasRole(RoleName::STAFF);
    }

    public function viewInAdmin(User $user)
    {
        return $user->hasRole(RoleName::ADMIN) || $user->hasRole(RoleName::STAFF);
    }

    public function viewInProfile(User $user, Order $order)
    {
        return $order->user_id === $user->id;
    }

    public function modify(User $user)
    {
        return $user->hasRole(RoleName::ADMIN) || $user->hasRole(RoleName::STAFF);
    }

    public function cancel(User $user, Order $order)
    {
        return $user->hasRole(RoleName::ADMIN) || $user->hasRole(RoleName::STAFF) || $order->user_id === $user->id;
    }

    public function confirm(User $user, Order $order)
    {
        return $user->hasRole(RoleName::ADMIN) || $user->hasRole(RoleName::STAFF) || $order->user_id === $user->id;
    }

    public function continueOrder(User $user, Order $order)
    {
        return $order->user_id === $user->id;
    }
}
