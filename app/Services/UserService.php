<?php

namespace App\Services;

use App\Models\Country;
use App\Models\User;
use App\Models\UserDeactivationSnapshot;
use App\Notifications\User\AdminDeactivatedUserAccountNotification;
use App\Notifications\User\AdminDeletedUserAccountNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;

class UserService
{
    public function getUsers($paginate = false)
    {
        return $paginate ? User::withTrashed()->paginate(15, ['*'], 'users_page') : User::withTrashed()->get();
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

    public function getProfile()
    {
        return Country::all(['id', 'name', 'iso_code']);
    }

    public function updateFirstTimeLogin(User $user)
    {
        $user->update(['is_first_login' => false]);
    }
}
