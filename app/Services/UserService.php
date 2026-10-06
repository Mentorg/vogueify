<?php

namespace App\Services;

use App\Enums\AggregatedOrderStatus;
use App\Models\Country;
use App\Models\User;
use App\Models\UserDeactivationSnapshot;
use App\Notifications\User\AdminDeactivatedUserAccountNotification;
use App\Notifications\User\AdminDeletedUserAccountNotification;
use App\Notifications\User\AdminRestoredUserAccountNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;

class UserService
{
    public function getUsers($paginate = false)
    {
        return $paginate ? User::withTrashed()->paginate(15, ['*'], 'users_page') : User::withTrashed()->get();
    }

    public function getUser(User $user, string $tab = 'personal-information'): array
    {
        $user->load('deactivationSnapshot');

        return [
            'user' => $user,
            'orders' => $tab === 'orders' ? $user->orders()->latest()->paginate(5, ['*'], 'orders_page')->withQueryString() : null,
            'invoices' => $tab === 'invoices' ? $user->invoices()->with('user:id,name')->latest()->paginate(5, ['*'], 'invoices_page')->withQueryString() : null,
            'coupons' => $tab === 'coupons' ? $user->coupons()->latest()->paginate(5, ['*'], 'coupons_page')->withQueryString() : null,
            'orderStatuses' => AggregatedOrderStatus::values(),
        ];
    }

    public function forceDelete(User $user): void
    {
        if (!$user->trashed()) {
            throw ValidationException::withMessages([
                'user' => 'Only deactivated accounts can be permanently deleted.',
            ]);
        }

        if ($user->orders()->exists() || $user->invoices()->exists()) {
            throw ValidationException::withMessages([
                'user' => 'Users with existing transactions cannot be permanently deleted.',
            ]);
        }

        $originalEmail = $user->email;

        $user->forceDelete();

        Notification::route('mail', $originalEmail)->notify(new AdminDeletedUserAccountNotification());
    }

    public function deactivate(User $user): void
    {
        if ($user->trashed()) {
            throw ValidationException::withMessages([
                'user' => 'User is already deactivated!'
            ]);
        }

        $originalName = $user->name;
        $originalEmail = $user->email;

        DB::transaction(function () use ($user) {
            UserDeactivationSnapshot::create([
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'date_of_birth' => $user->date_of_birth,
            ]);

            $user->forceFill([
                'name' => 'Deleted User',
                'date_of_birth' => null,
                'email' => "deleted+{$user->id}@deleted.invalid",
                'email_verified_at' => null,
            ]);

            $user->save();

            $user->deleteProfilePhoto();
            $user->tokens->each->delete();
            $user->delete();
        });

        Notification::route('mail', $originalEmail)->notify(new AdminDeactivatedUserAccountNotification($originalName));
    }

    public function restore(User $user): void
    {
        if (! $user->trashed()) {
            throw ValidationException::withMessages([
                'user' => 'User is not deactivated!',
            ]);
        }

        $snapshot = $user->deactivationSnapshot;

        if (! $snapshot) {
            throw ValidationException::withMessages([
                'user' => 'The user restoration data could not be found.',
            ]);
        }

        $originalEmail = $snapshot->email;

        DB::transaction(function () use ($user, $snapshot) {
            $emailInUse = User::query()
                ->where('email', $snapshot->email)
                ->whereKeyNot($user->id)
                ->exists();

            if ($emailInUse) {
                throw ValidationException::withMessages([
                    'email' => 'The original email address is already in use by another user.',
                ]);
            }

            $user->forceFill([
                'name' => $snapshot->name,
                'email' => $snapshot->email,
                'date_of_birth' => $snapshot->date_of_birth,
            ]);

            $user->restore();

            $snapshot->delete();
        });

        Notification::route('mail', $originalEmail)
            ->notify(new AdminRestoredUserAccountNotification());
    }

    public function getProfile()
    {
        return Country::all(['id', 'name', 'iso_code']);
    }

    public function updateFirstTimeLogin(User $user)
    {
        $user->update(['is_first_login' => false]);
    }
}
