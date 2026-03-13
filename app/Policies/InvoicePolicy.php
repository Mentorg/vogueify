<?php

namespace App\Policies;

use App\Enums\InvoiceStatus;
use App\Enums\RoleName;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class InvoicePolicy
{
    public function before(User $user, $ability)
    {
        if ($user->hasRole(RoleName::ADMIN)) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(RoleName::STAFF);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Invoice $invoice): bool
    {
        return $user->hasRole(RoleName::STAFF) || $invoice->user_id === $user->id;
    }

    /**
     * Determine whether the user can download the model.
     */
    public function download(User $user, Invoice $invoice): bool
    {
        return $user->hasRole(RoleName::STAFF);
    }

    /**
     * Determine whether the user can resend the model.
     */
    public function resend(User $user, Invoice $invoice): bool
    {
        return $user->hasRole(RoleName::STAFF);
    }

    /**
     * Determine whether the user can cancel the model.
     */
    public function cancel(User $user, Invoice $invoice): bool
    {
        return $user->hasRole(RoleName::STAFF) && $invoice->status !== InvoiceStatus::PAID;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function updateNote(User $user, Invoice $invoice): bool
    {
        return $user->hasRole(RoleName::STAFF);
    }
}
