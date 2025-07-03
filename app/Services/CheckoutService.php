<?php

namespace App\Services;

class CheckoutService
{
    public function getCheckout($request)
    {
        $user = $request->user();

        if (!$user || !$user->cart || $user->cart->cartItems->isEmpty()) {
            abort(404, 'No cart found for checkout.');
        }

        $user->load([
            'cart.cartItems.productVariation.product',
            'address'
        ]);

        $cartItems = $user->cart->cartItems;

        $subtotal = $cartItems->sum(fn ($item) => $item->price_at_time * $item->quantity);
        $shipping = 0.00;
        $tax = 0.00;
        $total = $subtotal + $shipping + $tax;

        return [
            'user' => $user,
            'cart' => $user->cart,
            'address' => $user->address,
            'subtotal' => round($subtotal, 2),
            'shipping' => round($shipping, 2),
            'tax' => round($tax, 2),
            'total' => round($total, 2)
        ];
    }
}
