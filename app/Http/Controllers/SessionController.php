<?php

namespace App\Http\Controllers;

use App\Services\SessionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionController extends Controller
{
    protected $sessionService;

    public function __construct(SessionService $sessionService)
    {
        $this->sessionService = $sessionService;
    }

    public function store(Request $request)
    {
        $this->sessionService->create($request);

        $user = Auth::user();

        if ($user && !$user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        return redirect('/')->withErrors([
            'email' => 'The provided credentials do not match our records'
        ]);
    }

    public function destroy(Request $request)
    {
        $this->sessionService->delete($request);

        return redirect('/login');
    }
}
