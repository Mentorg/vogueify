<?php

namespace App\Services;

use App\Enums\AggregatedOrderStatus;
use App\Enums\OrderStatus;
use App\Models\Order;
use App\Notifications\Order\RequestOrderConfirmationNotification;
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

            if (!$orderId) {
                Log::warning('Order ID not found in session metadata');
                return response()->json(['status' => 'no_order_id'], 400);
            }

            $order = Order::with(['items.productVariation', 'cart.cartItems'])->find($orderId);

            if (!$order) {
                Log::warning('Order not found', ['order_id' => $orderId]);
                return response()->json(['status' => 'order_not_found'], 404);
            }

            if ($order->order_status === AggregatedOrderStatus::Paid) {
                return response()->json(['status' => 'already_paid']);
            }

            $order->order_status = AggregatedOrderStatus::Paid;
            foreach ($order->items as $item) {
                $item->order_status = OrderStatus::Paid;
                $item->save();
            }
            $order->save();

            if ($order->cart) {
                $order->cart->cartItems()->delete();
                $order->cart->is_locked = false;
                $order->cart->save();
            } else {
                Log::warning('Order has no associated cart', [
                    'order_id' => $order->id,
                    'cart_id' => $order->cart_id,
                ]);
            }

            $order->user->notify(new RequestOrderConfirmationNotification($order));

            return response()->json(['status' => 'success']);
        }
    }
}
