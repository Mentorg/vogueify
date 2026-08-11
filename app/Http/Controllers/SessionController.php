<?php

namespace App\Http\Controllers;

use App\Services\SessionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionController extends Controller
{
    protected $sessionService;

    public function __construct(SessionService $sessionService)
    {
        $this->sessionService = $sessionService;
    }

    public function store(Request $request): RedirectResponse
    {
        if (!$this->sessionService->create($request)) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');
        }

        $user = Auth::user();

        if ($user && !$user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        return redirect()->intended('/');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $this->sessionService->delete($request);

        return redirect('/login');
    }
}
