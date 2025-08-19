<?php

namespace App\Services;

use App\Models\Order;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use Illuminate\Support\Facades\Auth;
use Stripe\PaymentIntent;

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

    public function createSession(Order $order)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $lineItems = [];

        foreach ($order->items as $item) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $item->productVariation->product->name,
                        'images' => [asset('storage/' . $item->productVariation->image)]
                    ],
                    'unit_amount' => intval($item->price_at_time * 100),
                ],
                'quantity' => $item->quantity,
            ];
        }

        if ($order->shipping_cost > 0) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => 'Shipping Cost',
                    ],
                    'unit_amount' => intval($order->shipping_cost * 100),
                ],
                'quantity' => 1,
            ];
        }

        if ($order->tax_amount > 0) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => 'Tax',
                    ],
                    'unit_amount' => intval($order->tax_amount * 100),
                ],
                'quantity' => 1,
            ];
        }

        $checkoutSession = StripeSession::create([
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => route('checkout.success') . '?session_id={CHECKOUT_SESSION_ID}',
            // 'cancel_url' => route('checkout.cancel', ['order_id' => $order->id]),
            'metadata' => [
                'order_id' => $order->id,
            ],
            'customer_email' => $order->user->email,
        ]);

        return $checkoutSession;
    }

    public function success($request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $session = StripeSession::retrieve($request->get('session_id'));
        $orderId = $session->metadata->order_id ?? null;

        $paymentIntent = PaymentIntent::retrieve($session->payment_intent);

        $order = null;
        if ($orderId) {
            $order = Order::with(['items.productVariation.product', 'items.size'])->find($orderId);
        }

        return [
            'session' => $session,
            'paymentIntent' => $paymentIntent,
            'order' => $order
        ];
    }
}
