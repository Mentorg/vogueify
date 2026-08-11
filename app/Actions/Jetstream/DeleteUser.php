<?php

namespace App\Actions\Jetstream;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Laravel\Jetstream\Contracts\DeletesUsers;

class DeleteUser implements DeletesUsers
{
    /**
     * Delete the given user.
     */
    public function delete(User $user): void
    {
        DB::transaction(function () use ($user) {
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
    }
}
