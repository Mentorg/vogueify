<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;

class SessionService
{
    public function create ($request)
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:users',
            'password' => 'required'
        ]);

        if (Auth::attempt($validated)) {
            $request->session()->regenerate();
        }
    }

    public function delete($request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();
    }
}
