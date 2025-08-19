<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Order;
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
        $order = Order::findOrFail($request->order_id);

        $checkoutSession = $this->checkoutService->createSession($order);

        return redirect()->away($checkoutSession->url);
    }

    public function success(Request $request)
    {
        return Inertia::render('CheckoutSuccess', [
            'checkoutSession' => $this->checkoutService->success($request)
        ]);
    }
}
