<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderShippingAddressRequest extends FormRequest
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
            'shipping_address_line_1' => 'required|string|max:255',
            'shipping_address_line_2' => 'nullable|string|max:255',
            'shipping_city' => 'required|string|min:2|max:255',
            'shipping_state' => 'nullable|string|max:255',
            'shipping_postcode' => 'required|string|alpha_num',
            'shipping_country_id' => 'required|integer|exists:countries,id',
            'shipping_phone_number' => 'nullable|string|min:7|max:20',
            'shipping_date' => 'nullable|date',
        ];
    }
}
