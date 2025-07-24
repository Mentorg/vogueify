<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;
use UnexpectedValueException;

class WebhookService
{
    public function handle($request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (UnexpectedValueException $e) {
            Log::error('Invalid Stripe webhook payload', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Invalid payload'], 400);
        } catch (SignatureVerificationException $e) {
            Log::error('Invalid Stripe webhook signature', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Invalid signature'], 400);
        }

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;
            $orderId = $session->metadata->order_id ?? null;

            if ($orderId) {
                $order = Order::with(['items.productVariation', 'cart.cartItems'])->find($orderId);

                if ($order && $order->order_status !== 'paid') {
                    Log::warning('Order has no associated cart', ['order_id' => $orderId]);
                    $order->order_status = OrderStatus::Paid;
                    $order->save();

                    if ($order->cart) {
                        $order->cart->cartItems()->delete();
                        $order->cart->is_locked = false;
                        $order->cart->save();
                    } else {
                        Log::warning('Order has no associated cart', ['order_id' => $orderId]);
                    }
                }
            } else {
                Log::warning('Order ID not found in session metadata');
            }
        }

        return response()->json(['status' => 'success']);
    }
}
