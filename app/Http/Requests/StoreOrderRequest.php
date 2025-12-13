<?php

namespace App\Http\Requests;

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
            'shipping_date' => 'nullable|date',
            'order_date' => 'nullable|date',
            'coupon' => 'nullable|string',
            'discount_amount' => 'required',

            'shipping_address_line_1' => 'required|string|max:255',
            'shipping_address_line_2' => 'nullable|string|max:255',
            'shipping_city' => 'required|string|max:255',
            'shipping_state' => 'nullable|string|max:255',
            'shipping_postcode' => 'required|string',
            'shipping_country_id' => 'required|integer|exists:countries,id',
            'shipping_phone_number' => 'nullable|string|max:20',

            'billing_address_line_1' => 'nullable|string|max:255',
            'billing_address_line_2' => 'nullable|string|max:255',
            'billing_city' => 'nullable|string|max:255',
            'billing_state' => 'nullable|string|max:255',
            'billing_postcode' => 'nullable|string',
            'billing_country_id' => 'nullable|integer|exists:countries,id',
            'billing_phone_number' => 'nullable|string|max:20',

            'items' => 'required|array|min:1',
            'items.*.product_variation_id' => 'required|exists:product_variations,id',
            'items.*.size_id' => 'nullable|exists:sizes,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price_at_time' => 'required|numeric|min:0.01',
        ];
    }
}
