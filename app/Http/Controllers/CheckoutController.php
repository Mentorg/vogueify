<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Services\CheckoutService;
use Illuminate\Http\Request;
use Inertia\Inertia;
class CheckoutController extends Controller
{
    protected $checkoutService;

    public function __construct(CheckoutService $checkoutService)
    {
        $this->checkoutService = $checkoutService;
    }

    public function index(Request $request)
    {
        return Inertia::render('Checkout', [
            'checkoutData' => $this->checkoutService->getCheckout($request),
            'countries' => Country::all(['id', 'name', 'iso_code'])
        ]);
    }

    public function createSession(Request $request)
    {
        $checkoutSession = $this->checkoutService->createSession($request);

        return response()->json([
            'redirect_url' => $checkoutSession->url,
        ]);
    }

    public function success(Request $request)
    {
        return Inertia::render('CheckoutSuccess', [
            'checkoutSession' => $this->checkoutService->success($request)
        ]);
    }
}
