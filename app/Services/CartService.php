<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CartService
{
    public function getCartItems()
    {
        if (!Auth::id()) {
            abort(404);
        }

        $cart = Cart::where('user_id', Auth::id())->first();

        if (!$cart) {
            return collect();
        }

        $cartItems = $cart->cartItems()->with([
            'productVariation.product',
            'productVariation.sizes',
            'size'
        ])->get();

        $wishlistIds = Auth::user()
            ->wishlist()
            ->pluck('product_variation_id')
            ->toArray();

        $cartItems->each(function ($item) use ($wishlistIds) {
            $item->productVariation->isInWishlist = in_array($item->productVariation->id, $wishlistIds);
        });

        $subtotal = $cartItems->sum(fn($item) => $item->price_at_time * $item->quantity);
        $shipping = 0.00;
        $tax = 0.00;
        $total = $subtotal + $shipping + $tax;

        return [
            'cartItems' => $cartItems,
            'subtotal' => round($subtotal, 2),
            'shipping' => round($shipping, 2),
            'tax' => round($tax, 2),
            'total' => round($total, 2),
        ];
    }

    public function create($request)
    {
        if (!$request->user()) {
            abort(404);
        }

        $validated = $request->validate([
            'quantity' => "numeric|min:1",
            'price_at_time' => "numeric|min:0.01",
            'cart_id' => "exists:carts,id",
            'product_variation_id' => "exists:product_variations,id",
            'size_id' => "nullable|exists:sizes,id",
        ]);

        $cart = Cart::where('user_id', Auth::id())->first();

        if (!$cart) {
            $cart = Cart::create(['user_id' => Auth::id()]);
        }

        if ($cart->is_locked) {
            throw ValidationException::withMessages([
                'error' => 'You cannot modify the cart until payment is completed.',
            ]);
        }

        $cart_item = $cart->cartItems()->create([
            'quantity' => $validated['quantity'],
            'price_at_time' => $validated['price_at_time'],
            'product_variation_id' => $validated['product_variation_id'],
            'size_id' => $validated['size_id'],
        ]);

        $cart_item->save();
    }

    public function delete($id)
    {
        $cartItem = CartItem::findOrFail($id);

        $cart = $cartItem->cart;

        $cartItem->delete();

        if ($cart->cartItems()->count() === 0) {
            $cart->is_locked = false;
            $cart->save();
        }
    }
}
