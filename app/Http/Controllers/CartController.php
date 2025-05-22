<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
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
        return Inertia::render('Cart', [
            'cartItems' => $this->cartService->getCartItems()
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
