<?php

namespace App\Services;

use App\Models\User;

class RegisterUserService
{
    public function create($request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed'
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password'])
        ]);

        $token = $user->createToken($request->name);

        return [
            'user' => $user->save(),
            'token' => $token
        ];
    }
}
