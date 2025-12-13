<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCouponRequest extends FormRequest
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
            'couponType' => 'required',
            'code' => 'required|string',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0.01',
            'starts_at' => 'required|date',
            'expires_at' => 'required|date',
            'status' => 'required|in:active,inactive,archived',
            'max_uses' => 'required|integer|min:0',
            'max_uses_per_user' => 'nullable|integer|min:0',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:product_categories,id',
            'products' => 'nullable|array',
            'products.*' => 'exists:products,id',
            'productVariations' => 'nullable|array',
            'productVariations.*' => 'exists:product_variations,id',
            'users' => 'nullable|array',
            'users.*' => 'exists:users,id'
        ];
    }
}
