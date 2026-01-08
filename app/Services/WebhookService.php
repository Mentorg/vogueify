<?php

namespace App\Services;

use App\Enums\AggregatedOrderStatus;
use App\Events\Order\OrderPaid;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\Webhook;

class WebhookService
{
    public function handle($request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (\Exception $e) {
            Log::error('Stripe Webhook Error', [
            'message' => $e->getMessage()
        ]);
            return response()->json(['message' => 'Invalid webhook'], 400);
        }

        if ($event->type !== 'checkout.session.completed') {
            return response()->json(['message' => 'ignored'], 200);
        }

        $session = $event->data->object;
        $orderId = $session->metadata->order_id ?? null;

        if (!$orderId) {
            Log::warning('Stripe webhook missing order ID');
            return response()->json(['message' => 'missing_order_id'], 200);
        }

        $order = Order::with([
            'user',
            'items.productVariation.product',
            'items.size',
            'cart.cartItems'
        ])->find($orderId);

        if (!$order) {
            Log::warning('Stripe webhook order not found', ['order_id' => $orderId]);
            return response()->json(['message' => 'order_not_found'], 200);
        }

        if ($order->order_status === AggregatedOrderStatus::Paid) {
            return response()->json(['message' => 'already_paid'], 200);
        }

        DB::transaction(function () use ($order) {

            $order->order_status = AggregatedOrderStatus::Paid;
            $order->save();

            event(new OrderPaid($order));
        });

        Log::info('Order marked as paid via Stripe webhook', [
            'order_id' => $order->id
        ]);

        return response()->json(['message' => 'success'], 200);
    }
}
