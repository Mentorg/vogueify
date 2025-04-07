<?php

namespace App\Services;

use App\Models\User;

class RegisterUserService
{
    public function create(array $fields, $request)
    {
        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password'])
        ]);

        $token = $user->createToken($request->name);

        return [
            'user' => $user->save(),
            'token' => $token
        ];
    }
}
