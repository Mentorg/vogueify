<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'gender' => 'required|in:men,women,unisex',
            'features' => 'required|array',
            'features.*.title' => 'required|string|max:255',
            'features.*.description' => 'required|string|max:255',
            'category_id' => 'required|exists:product_categories,id',
            'image' => 'nullable|image|max:2048',
            'color' => 'required|string',
            'type' => 'required|string',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'sku' => 'required|string|max:255'
        ];
    }
}
