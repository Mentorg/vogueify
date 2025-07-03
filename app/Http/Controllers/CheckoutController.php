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
}
