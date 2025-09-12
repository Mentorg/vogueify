<?php

namespace App\Services;

use App\Enums\AggregatedOrderStatus;
use App\Enums\OrderStatus;
use App\Models\Order;
use App\Notifications\Order\RequestOrderConfirmationNotification;
use Exception;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use Illuminate\Support\Facades\Log;
use Stripe\PaymentIntent;

class CheckoutService
{
    public function getCheckout($request)
    {
        $user = $request->user();

        if (!$user) {
            abort(404);
        }

        $user->load([
            'cart.cartItems.productVariation.product',
            'address',
            'orders.items.productVariation.product'
        ]);

        $pendingOrder = $user->orders
            ->where('order_status', AggregatedOrderStatus::Pending->value)
            ->sortByDesc('created_at')
            ->first();

        $cartItems = $user->cart?->cartItems;

        if (!$cartItems || $cartItems->isEmpty()) {
            abort(404, 'No cart found for checkout.');
        }

        $subtotal = $cartItems->sum(fn ($item) => $item->price_at_time * $item->quantity);
        $shipping = 0.00;
        $tax = 0.00;
        $total = $subtotal + $shipping + $tax;

        return [
            'user' => $user,
            'cart' => $user->cart,
            'address' => $user->address,
            'pendingOrder' => $pendingOrder,
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

        $sessionId = $request->get('session_id');

        if (!$sessionId) {
            throw new Exception('Missing Stripe session ID.');
        }

        $session = StripeSession::retrieve($sessionId);
        $paymentIntent = PaymentIntent::retrieve($session->payment_intent);

        $orderId = $session->metadata->order_id ?? null;

        if (!$orderId) {
            throw new Exception('Order ID not found in session metadata.');
        }

        $order = Order::with(['items.productVariation.product', 'items.size', 'cart.cartItems', 'user'])->find($orderId);

        if (!$order) {
            throw new Exception("Order with ID {$orderId} not found.");
        }

        if (
            $session->payment_status === 'paid' &&
            $order->order_status !== AggregatedOrderStatus::Paid
        ) {
            $order->order_status = AggregatedOrderStatus::Paid;
            $order->save();

            foreach ($order->items as $item) {
                $item->order_status = OrderStatus::Paid;
                $item->save();
            }

            if ($order->cart) {
                $order->cart->cartItems()->delete();
                $order->cart->is_locked = false;
                $order->cart->save();
            }

            $order->user->notify(new RequestOrderConfirmationNotification($order));

            Log::info('Order marked as paid via success page.', [
                'order_id' => $order->id,
                'session_id' => $sessionId,
            ]);
        }

        return [
            'session' => $session,
            'paymentIntent' => $paymentIntent,
            'order' => $order
        ];
    }
}
