<?php

namespace App\Services;

use App\Enums\AggregatedOrderStatus;
use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\ProductVariation;
use App\Notifications\Order\OrderBillingAddressUpdatedNotification;
use App\Notifications\Order\OrderCancelledNotification;
use App\Notifications\Order\OrderConfirmedNotification;
use App\Notifications\Order\OrderItemShippingDateUpdatedNotification;
use App\Notifications\Order\OrderItemStatusUpdatedNotification;
use App\Notifications\Order\OrderShippingAddressUpdatedNotification;
use App\Notifications\Order\OrderStatusUpdatedNotification;
use App\Notifications\Order\RequestOrderConfirmationNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class OrderService
{
    protected function handleOrderAttributes(array $validated, float $subtotal, float $shippingCost, float $taxAmount, float $total, int $cartId): array
    {
        return [
            'order_date' => now()->format('Y-m-d H:i:s'),
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
            'shipping_country_id' => $validated['shipping_country_id'],
            'shipping_phone_number' => $validated['shipping_phone_number'] ?? null,

            'billing_address_line_1' => $validated['billing_address_line_1'] ?? null,
            'billing_address_line_2' => $validated['billing_address_line_2'] ?? null,
            'billing_city' => $validated['billing_city'] ?? null,
            'billing_state' => $validated['billing_state'] ?? null,
            'billing_postcode' => $validated['billing_postcode'] ?? null,
            'billing_country_id' => $validated['billing_country_id'] ?? null,
            'billing_phone_number' => $validated['billing_phone_number'] ?? null,

            'order_status' => AggregatedOrderStatus::Pending,
        ];
    }

    public function getOrders()
    {
        return Order::with(['user', 'items.productVariation.product'])->orderBy('created_at', 'desc')->paginate(15);
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
            $existingPendingOrder = $user->orders()
                ->where('order_status', AggregatedOrderStatus::Pending->value)
                ->first();

            if ($existingPendingOrder) {
                $order = $existingPendingOrder;
                $order->items()->delete();
            }

            $cart = $user->cart;

             if (!$cart) {
                throw new Exception('No cart found!');
            }

            if ($cart->is_locked && (!$existingPendingOrder || $existingPendingOrder->cart_id !== $cart->id)) {
                throw new Exception('Cart is already in use by another order.');
            }

            $cart->is_locked = true;
            $cart->save();

            $subtotal = collect($validated['items'])->sum(function ($item) {
                return $item['quantity'] * $item['price_at_time'];
            });

            $shippingCost = 5.00;
            $taxAmount = $subtotal * 0.10;
            $total = $subtotal + $shippingCost + $taxAmount;

            $orderAttributes = $this->handleOrderAttributes(
                $validated,
                $subtotal,
                $shippingCost,
                $taxAmount,
                $total,
                $cart->id
            );

            if ($order->exists) {
                $order->fill($orderAttributes);
                $order->save();
            } else {
                $order = Order::create($orderAttributes);
            }

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
                    'order_status' => OrderStatus::Pending,
                ]);

                if (!is_null($variation->stock)) {
                    $variation->stock -= $itemData['quantity'];
                    $variation->save();
                }
            }

            DB::commit();

            return $order->load('items.productVariation', 'items.size');
        } catch (ValidationException $e) {
            DB::rollBack();
            Log::error($e);
            throw $e;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            throw new Exception('Failed to store the order. Reason: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }

    public function getOrder($order)
    {
        if (!Auth::id()) {
            abort(404);
        }

        $order->load('items.productVariation.product', 'items.productVariation.type', 'items.productVariation.color', 'items.size', 'user.orders');

        $subtotal = $order->items->sum(fn ($item) => $item->price_at_time * $item->quantity);
        $shipping = 0.00;
        $tax = 0.00;
        $total = $subtotal + $shipping + $tax;

        return [
            'order' => $order,
            'orderStatuses' => AggregatedOrderStatus::values(),
            'subtotal' => number_format($subtotal, 2, '.', ''),
            'shipping' => number_format($shipping, 2, '.', ''),
            'tax' => number_format($tax, 2, '.', ''),
            'total' => number_format($total, 2, '.', ''),
        ];
    }

    public function getUserOrders($request)
    {
        if (Auth::check()) {
            return $request->user()->orders()->with('items.productVariation')->orderBy('created_at', 'desc')->get();
        } else {
            abort(404);
        }
    }

    public function cancel($order)
    {
        $order->refresh();

        if ($order->order_status !== AggregatedOrderStatus::Pending) {
            throw new Exception('Only pending orders can be cancelled.');
        }

        DB::beginTransaction();

        try {
            $order->order_status = AggregatedOrderStatus::Canceled;
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

            $order->user->notify(new OrderCancelledNotification($order));

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('Failed to cancel the order. Reason: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }

    public function confirm(Order $order): array
    {
        if ($order->order_status !== AggregatedOrderStatus::Paid) {
            return [
                'status' => 'info',
                'message' => 'This order cannot be confirmed.',
            ];
        }

        DB::beginTransaction();

        try {
            $order->update([
                'order_status' => AggregatedOrderStatus::Confirmed
            ]);

            foreach ($order->items as $item) {
                $item->order_status = OrderStatus::Confirmed;
                $item->save();
            }

            $order->user->notify(new OrderConfirmedNotification($order));

            DB::commit();

            return [
                'status' => 'success',
                'message' => 'Order confirmed successfully!',
            ];
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('Failed to confirm the order. Reason: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }

    public function updateStatus($order, $newStatus)
    {
        $validStatuses = AggregatedOrderStatus::values();

        if (!in_array($newStatus, $validStatuses)) {
            throw new Exception('Invalid order status: ' . $newStatus);
        }

        DB::transaction(function () use ($order, $newStatus) {
            $previousStatus = $order->order_status;

            if (
                $previousStatus === AggregatedOrderStatus::Pending &&
                $newStatus === AggregatedOrderStatus::Canceled
            ) {
                if ($order->cart) {
                    $order->cart->cartItems()->delete();
                    $order->cart->is_locked = false;
                    $order->cart->save();
                }
            }
        });

        $order->user->notify(new OrderStatusUpdatedNotification($order));

        $order->order_status = $newStatus;
        $order->save();
    }

    public function updateItemStatus($orderItem, $newStatus)
    {
        $validStatuses = OrderStatus::values();

        if (!in_array($newStatus, $validStatuses)) {
            throw new Exception('Invalid order item status: ' . $newStatus);
        }

        $orderItem->order->user->notify(new OrderItemStatusUpdatedNotification($orderItem));

        $orderItem->order_status = $newStatus;
        $orderItem->save();
    }

    public function updateItemShippingDate($orderItem, $newDate)
    {
        $orderItem->shipping_date = $newDate;

        $orderItem->order->user->notify(new OrderItemShippingDateUpdatedNotification($orderItem));

        $orderItem->save();
    }

    public function updateNote($order, $newNote)
    {
        $order->order_note = $newNote;
        $order->save();
    }

    public function updateShippingAddress($order, $validated)
    {
        $order->update(Arr::only($validated,[
            'shipping_address_line_1',
            'shipping_address_line_2',
            'shipping_city',
            'shipping_state',
            'shipping_postcode',
            'shipping_country_id',
            'shipping_phone_number',
            'shipping_date',
        ]));

        $order->load('shippingCountry');

        $order->user->notify(new OrderShippingAddressUpdatedNotification($order));
    }

    public function updateBillingAddress($order, array $validated)
    {
        $order->update(Arr::only($validated, [
            'billing_address_line_1',
            'billing_address_line_2',
            'billing_city',
            'billing_state',
            'billing_postcode',
            'billing_country_id',
            'billing_phone_number',
        ]));

        $order->load('billingCountry');

        $order->user->notify(new OrderBillingAddressUpdatedNotification($order));
    }

    public function resendConfirmation($order)
    {
        $order->user->notify(new RequestOrderConfirmationNotification($order));
    }
}
