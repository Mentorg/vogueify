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
        return $this->registerUserService->create($request);
    }
}
