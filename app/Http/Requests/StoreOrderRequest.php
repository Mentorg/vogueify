<?php

namespace App\Http\Requests;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'order_number' => 'required|unique:orders,order_number',
            'shipping_date' => 'nullable|date',
            'order_date' => 'required|date',
            'user_id' => 'required|exists:users,id',

            'shipping_address_line_1' => 'required|string|max:255',
            'shipping_address_line_2' => 'nullable|string|max:255',
            'shipping_city' => 'required|string|max:255',
            'shipping_state' => 'nullable|string|max:255',
            'shipping_postcode' => 'required|string|max:10',
            'shipping_country' => 'required|string|max:255',
            'shipping_phone_number' => 'nullable|string|max:20',

            'billing_address_line_1' => 'nullable|string|max:255',
            'billing_address_line_2' => 'nullable|string|max:255',
            'billing_city' => 'nullable|string|max:255',
            'billing_state' => 'nullable|string|max:255',
            'billing_postcode' => 'nullable|string|max:10',
            'billing_country' => 'nullable|string|max:255',
            'billing_phone_number' => 'nullable|string|max:20',

            'order_status' => 'required|in:' . implode(',', [
                Order::STATUS_ORDER_PLACED,
                Order::STATUS_ORDER_CONFIRMED,
                Order::STATUS_ORDER_PROCESSING,
                Order::STATUS_SHIPPED,
                Order::STATUS_IN_TRANSIT,
                Order::STATUS_OUT_FOR_DELIVERY,
                Order::STATUS_DELIVERED,
                Order::STATUS_ATTEMPTED_DELIVERY,
                Order::STATUS_CANCELED,
                Order::STATUS_HELD_AT_CUSTOMS,
                Order::STATUS_AWAITING_PICKUP,
                Order::STATUS_DELAYED,
                Order::STATUS_LOST,
            ]),

            'items' => 'required|array|min:1',
            'items.*.product_variation_id' => 'required|exists:product_variations,id',
            'items.*.size_id' => 'nullable|exists:sizes,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price_at_time' => 'required|numeric|min:0.01',
        ];
    }
}
