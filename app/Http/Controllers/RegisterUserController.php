<?php

namespace App\Http\Controllers;

use App\Services\RegisterUserService;
use Illuminate\Http\Request;

class RegisterUserController extends Controller
{
    protected $registerUserService;

    public function __construct(RegisterUserService $registerUserService)
    {
        $this->registerUserService = $registerUserService;
    }

    public function store (Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed'
        ]);

        return $this->registerUserService->create($validated, $request);
    }
}
