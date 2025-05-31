<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariation;
use Exception;
use Illuminate\Support\Facades\DB;

class OrderService
{
    protected function handleOrderAttributes(array $validated, float $subtotal, float $shippingCost, float $taxAmount, float $total): array
    {
        return [
            'order_number' => $validated['order_number'],
            'shipping_date' => $validated['shipping_date'] ?? null,
            'order_date' => $validated['order_date'],
            'subtotal' => $subtotal,
            'shipping_cost' => $shippingCost,
            'tax_amount' => $taxAmount,
            'total' => $total,
            'user_id' => $validated['user_id'],

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

            'order_status' => $validated['order_status'],
        ];
    }

    public function create($order, $request)
    {
        $validated = $request->validated();

        DB::beginTransaction();

        try {
            $subtotal = collect($validated['items'])->sum(function ($item) {
                return $item['quantity'] * $item['price_at_time'];
            });

            $shippingCost = 5.00;
            $taxAmount = $subtotal * 0.10;
            $total = $subtotal + $shippingCost + $taxAmount;

            $order = Order::create(
                $this->handleOrderAttributes($validated, $subtotal, $shippingCost, $taxAmount, $total)
            );

            foreach ($validated['items'] as $itemData) {
                $variation = ProductVariation::findOrFail($itemData['product_variation_id']);

                if (!$variation->hasSufficientStock($itemData['quantity'])) {
                    throw new \Exception("Insufficient stock for SKU {$variation->sku}");
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
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception('Failed to store the order. Reason: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }
}
