<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderBillingAddressRequest extends FormRequest
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
            'billing_address_line_1' => 'required|string|max:255',
            'billing_address_line_2' => 'nullable|string|max:255',
            'billing_city' => 'required|string|min:2|max:255',
            'billing_state' => 'nullable|string|max:255',
            'billing_postcode' => 'required|string|alpha_num',
            'billing_country_id' => 'nullable|integer|exists:countries,id',
            'billing_phone_number' => 'nullable|string|min:7|max:20',
        ];
    }
}
