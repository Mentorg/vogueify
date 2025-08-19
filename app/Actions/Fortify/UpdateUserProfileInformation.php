<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    /**
     * Validate and update the given user's profile information.
     *
     * @param  array<string, mixed>  $input
     */
    public function update(User $user, array $input): void
    {
        $validator = Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'title' => ['required', 'in:mr,ms'],
            'date_of_birth' => ['nullable', 'date'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'photo' => ['nullable', 'mimes:jpg,jpeg,png', 'max:1024'],
            'has_address' => ['boolean'],
        ]);

        if (!empty($input['has_address'])) {
            $validator->addRules([
                'address_line_1' => ['nullable', 'string', 'max:255'],
                'address_line_2' => ['nullable', 'string', 'max:255'],
                'city' => ['nullable', 'string', 'max:255'],
                'state' => ['nullable', 'string', 'max:255'],
                'postcode' => ['nullable', 'string', 'max:20'],
                'country_id' => ['nullable', 'exists:countries,id']
            ]);
        }

        $validator->validateWithBag('updateProfileInformation');

        if (isset($input['photo'])) {
            $user->updateProfilePhoto($input['photo']);
        }

        if ($input['email'] !== $user->email &&
            $user instanceof MustVerifyEmail) {
            $this->updateVerifiedUser($user, $input);
        } else {
            $user->forceFill([
                'name' => $input['name'],
                'email' => $input['email'],
                'title' => $input['title'],
                'date_of_birth' => $input['date_of_birth'],
            ])->save();
        }

        if (!empty($input['has_address'])) {
            $addressData = collect($input)->only([
                'address_line_1',
                'address_line_2',
                'city',
                'state',
                'postcode',
                'country_id',
                'phone_number',
            ])->toArray();

            $user->address()->updateOrCreate(
                ['user_id' => $user->id],
                $addressData
            );
        }
    }

    /**
     * Update the given verified user's profile information.
     *
     * @param  array<string, string>  $input
     */
    protected function updateVerifiedUser(User $user, array $input): void
    {
        $user->forceFill([
            'name' => $input['name'],
            'email' => $input['email'],
            'title' => $input['title'],
            'date_of_birth' => $input['date_of_birth'],
            'email_verified_at' => null,
        ])->save();

        $user->sendEmailVerificationNotification();
    }
}
