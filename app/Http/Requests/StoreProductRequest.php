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
            'features' => 'required|array',
            'features.*.title' => 'required|string|max:255',
            'features.*.description' => 'required|string|max:255',
            'gender' => 'required|in:men,women,unisex',
            'category_id' => 'required|exists:product_categories,id',

            'variations' => 'required|array|min:1',
            'variations.*.image' => 'required|image|max:2048',
            'variations.*.color_id' => 'nullable|exists:colors,id',
            'variations.*.primary_color_id' => 'nullable|exists:colors,id',
            'variations.*.secondary_color_id' => 'nullable|exists:colors,id',
            'variations.*.product_type_id' => 'required|exists:product_types,id',
            'variations.*.price' => 'required|numeric|min:0.01',
            'variations.*.sku' => 'required|string|max:255|distinct|unique:product_variations,sku',
            'variations.*.stock' => 'nullable|integer|min:0',
            'variations.*.sizes' => 'nullable|array',
            'variations.*.sizes.*.id' => 'required|exists:sizes,id',
            'variations.*.sizes.*.stock' => 'required|integer|min:0',
        ];
    }

    public function attributes()
    {
        return [
            'features.*.title' => 'feature title',
            'features.*.description' => 'feature description',
            'variations.*.image' => 'variation image',
            'variations.*.product_type_id' => 'product type',
            'variations.*.price' => 'price',
            'variations.*.sku' => 'sku',
            'variations.*.sizes.*.stock' => 'stock',
        ];
    }
}
