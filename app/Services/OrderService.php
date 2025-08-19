<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\ProductVariation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class OrderService
{
    protected function handleOrderAttributes(array $validated, float $subtotal, float $shippingCost, float $taxAmount, float $total, int $cartId): array
    {
        return [
            'order_date' => now()->format('d-m-Y H:i'),
            'subtotal' => $subtotal,
            'shipping_cost' => $shippingCost,
            'tax_amount' => $taxAmount,
            'total' => $total,
            'user_id' => Auth::id(),
            'cart_id' => $cartId,

            'shipping_address_line_1' => $validated['shipping_address_line_1'],
            'shipping_address_line_2' => $validated['shipping_address_line_2'] ?? null,
            'shipping_city' => $validated['shipping_city'],
            'shipping_state' => $validated['shipping_state'] ?? null,
            'shipping_postcode' => $validated['shipping_postcode'],
            'shipping_country' => $validated['shipping_country'],
            'shipping_phone_number' => $validated['shipping_phone_number'] ?? null,

            'billing_address_line_1' => $validated['billing_address_line_1'] ?? null,
            'billing_address_line_2' => $validated['billing_address_line_2'] ?? null,
            'billing_city' => $validated['billing_city'] ?? null,
            'billing_state' => $validated['billing_state'] ?? null,
            'billing_postcode' => $validated['billing_postcode'] ?? null,
            'billing_country' => $validated['billing_country'] ?? null,
            'billing_phone_number' => $validated['billing_phone_number'] ?? null,

            'order_status' => OrderStatus::Pending,
        ];
    }

    public function create($order, $request)
    {
        $user = Auth::user();

        if (!$user) {
            abort(404);
        }

        $validated = $request->validated();

        DB::beginTransaction();

        try {
            if ($user->orders()->where('order_status', OrderStatus::Pending)->exists()) {
                throw new Exception('You already have a pending order. Please complete it before placing a new one.');
            }

            $cart = $user->cart;

            if (!$cart || $cart->is_locked) {
                throw new Exception('Cart is either empty or already in use.');
            }

            $cart->is_locked = true;
            $cart->save();

            $subtotal = collect($validated['items'])->sum(function ($item) {
                return $item['quantity'] * $item['price_at_time'];
            });

            $shippingCost = 5.00;
            $taxAmount = $subtotal * 0.10;
            $total = $subtotal + $shippingCost + $taxAmount;

            $order = Order::create(
                $this->handleOrderAttributes($validated, $subtotal, $shippingCost, $taxAmount, $total, $cart->id)
            );

            foreach ($validated['items'] as $itemData) {
                $variation = ProductVariation::findOrFail($itemData['product_variation_id']);

                if (!$variation->hasSufficientStock($itemData['quantity'])) {
                    throw new Exception("Insufficient stock for SKU {$variation->sku}");
                }

                $order->items()->create([
                    'product_variation_id' => $itemData['product_variation_id'],
                    'size_id' => $itemData['size_id'] ?? null,
                    'quantity' => $itemData['quantity'],
                    'price_at_time' => $itemData['price_at_time'],
                ]);

                if (!is_null($variation->stock)) {
                    $variation->stock -= $itemData['quantity'];
                    $variation->save();
                }
            }

            DB::commit();

            return $order->load('items.productVariation', 'items.size');
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('Failed to store the order. Reason: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }

    public function getOrder($order)
    {
        if (!Auth::id()) {
            abort(404);
        }

        $order->load('items.productVariation.product', 'items.size');

        $subtotal = $order->items->sum(fn ($item) => $item->price_at_time * $item->quantity);
        $shipping = 0.00;
        $tax = 0.00;
        $total = $subtotal + $shipping + $tax;

        return [
            'order' => $order,
            'subtotal' => round($subtotal, 2),
            'shipping' => round($shipping, 2),
            'tax' => round($tax, 2),
            'total' => round($total, 2)
        ];
    }

    public function getUserOrders($request)
    {
        if (Auth::check()) {
            return $request->user()->orders()->with('items.productVariation')->get();
        } else {
            abort(404);
        }
    }

    public function cancel($order)
    {
        $order->refresh();

        if ($order->order_status !== OrderStatus::Pending) {
            throw new Exception('Only pending orders can be cancelled.');
        }

        DB::beginTransaction();

        try {
            $order->order_status = OrderStatus::Canceled;
            $order->save();

            foreach ($order->items as $item) {
                $variation = $item->productVariation;
                if (!is_null($variation->stock)) {
                    $variation->stock += $item->quantity;
                    $variation->save();
                }
            }

            $user = Auth::user();
            if ($user && $user->cart && $user->cart->is_locked) {
                $user->cart->cartItems()->delete();
                $user->cart->is_locked = false;
                $user->cart->save();
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('Failed to cancel the order. Reason: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }
}
