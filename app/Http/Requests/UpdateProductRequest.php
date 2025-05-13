<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
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

            'variations' => 'required|array',
            'variations.*.id' => 'nullable|integer|exists:product_variations,id',
            'variations.*.image' => 'nullable', // handled below
            'variations.*.product_type_id' => 'required|exists:product_types,id',
            'variations.*.color_id' => 'nullable|exists:colors,id',
            'variations.*.primary_color_id' => 'nullable|exists:colors,id',
            'variations.*.secondary_color_id' => 'nullable|exists:colors,id',
            'variations.*.price' => 'required|numeric',
            'variations.*.sku' => 'required|string|max:255',

            'variations.*.sizes' => 'required|array',
            'variations.*.sizes.*.id' => 'required|exists:sizes,id',
            'variations.*.sizes.*.stock' => 'required|integer|min:0',
        ];
    }

    public function withValidator($validator)
    {
        $product = $this->route('product');

        $validator->after(function ($validator) use ($product) {
            foreach ($this->input('variations', []) as $index => $variation) {
                $variationId = $variation['id'] ?? null;

                if (!empty($variation['sku'])) {
                    $validator->addRules([
                        "variations.$index.sku" => [
                            'required',
                            'string',
                            'max:255',
                            Rule::unique('product_variations', 'sku')
                                ->ignore($variationId),
                        ]
                    ]);
                }

                $imageField = "variations.$index.image";

                if ($this->hasFile($imageField)) {
                    $validator->addRules([
                        $imageField => 'file|image|max:2048',
                    ]);
                } elseif (isset($variation['image']) && !filter_var($variation['image'], FILTER_VALIDATE_URL)) {
                    $validator->errors()->add($imageField, 'Invalid image URL.');
                }
            }
        });
    }
}
