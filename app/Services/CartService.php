<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;

class CartService
{
    public function getCartItems()
    {
        if (!auth()->id()) {
            abort(404);
        }
        $cart = Cart::where('user_id', auth()->id())->first();

        return $cart->cartItems()->with(['productVariation.product', 'productVariation.sizes', 'size'])->get();
    }

    public function create($request)
    {
        $validated = $request->validate([
            'quantity' => "numeric|min:1",
            'price_at_time' => "numeric|min:0.01",
            'cart_id' => "exists:carts,id",
            'product_variation_id' => "exists:product_variations,id",
            'size_id' => "nullable|exists:sizes,id",
        ]);

        $cart = Cart::where('user_id', auth()->id())->first();

        if (!$cart) {
            $cart = Cart::create(['user_id' => auth()->id()]);
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
        $cartItem = CartItem::find($id)->first();
        $cartItem->delete();
    }
}
