<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CartController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function index()
    {
        $couponCode = session('applied_coupon');

        return Inertia::render('Cart', [
            'cart' => $this->cartService->getCartItems($couponCode),
            'coupon' => $couponCode
        ]);
    }

    public function store(Request $request)
    {
        $this->cartService->create($request);

        return redirect()->back();
    }

    public function destroy($id)
    {
        $this->cartService->delete($id);

        return redirect()->back();
    }
}
