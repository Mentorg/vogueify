<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\FIlters\Search;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class ProductService
{
    public function getProducts($request)
    {
        $query = ProductVariation::with(['product.category', 'type']);

        if ($request->has('gender')) {
            $query->whereHas('product', fn($q) => $q->where('gender', $request->gender));
        }

        if ($request->has('category')) {
            $query->whereHas('product.category', fn($q) => $q->where('name', $request->category));
        }

        if ($request->has('type')) {
            $query->whereHas('type', fn($q) => $q->where('type', $request->type));
        }

        return $query->get();
    }

    public function getProduct($product, ?string $variation = null)
    {
        $product->load(
            'productVariations.sizes',
            'productVariations.color',
            'productVariations.primaryColor',
            'productVariations.secondaryColor',
            'productVariations.type'
        );

        $activeVariation = null;

        if ($variation) {
            $activeVariation = $product->productVariations->firstWhere('sku', $variation)
                ?? $product->productVariations->first();
        } else {
            $activeVariation = $product->productVariations->first();
        }

        return [
            'product' => $product,
            'activeVariation' => $activeVariation,
        ];
    }


    public function create(array $validated, $request)
    {
        DB::beginTransaction();

        try {
            $product = Product::create([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'features' => $validated['features'],
                'gender' => $validated['gender'],
                'category_id' => $validated['category_id'],
            ]);

            foreach ($validated['variations'] as $index => $variationData) {
                $imagePath = null;

                if ($request->hasFile("variations.$index.image")) {
                    $image = $request->file("variations.$index.image");
                    $imagePath = Storage::url($image->store('products', 'public'));
                }

                $variation = ProductVariation::create([
                    'product_id' => $product->id,
                    'image' => $imagePath,
                    'sku' => $variationData['sku'],
                    'price' => $variationData['price'],
                    'product_type_id' => $variationData['product_type_id'],
                    'color_id' => $variationData['color_id'] ?? null,
                    'primary_color_id' => $variationData['primary_color_id'] ?? null,
                    'secondary_color_id' => $variationData['secondary_color_id'] ?? null,
                ]);

                if (!empty($variationData['sizes'])) {
                    $pivotData = collect($variationData['sizes'])->mapWithKeys(fn($s) => [
                        $s['id'] => ['stock' => $s['stock']]
                    ])->toArray();

                    $variation->sizes()->sync($pivotData);
                }
            }

            DB::commit();
            return $product;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception(
                'Failed to store the product. Reason: ' . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    public function update($request, Product $product): void
    {
        DB::beginTransaction();

        try {
            $features = collect($request->features)->map(function ($feature) {
                return [
                    'title' => $feature['title'],
                    'description' => $feature['description'],
                ];
            })->all();

            $product->update([
                'name' => $request->name,
                'description' => $request->description,
                'gender' => $request->gender,
                'features' => $features,
                'category_id' => $request->category_id,
            ]);

            $imageMap = [];

            if ($request->hasFile('variation_images')) {
                foreach ($request->file('variation_images') as $index => $file) {
                    $variationId = $request->input("variation_image_indices.$index");

                    if ($variationId && $file instanceof UploadedFile) {
                        $storedPath = $file->store('products', 'public');
                        $imageUrl = Storage::url($storedPath);
                        $imageMap[$variationId] = $imageUrl;
                    }

                    $existingVariation = $product->productVariations->firstWhere('id', $variationId);

                    if ($existingVariation && $existingVariation->image) {
                        $oldPath = str_replace('/storage/', '', $existingVariation->image);
                        Storage::disk('public')->delete($oldPath);
                    }
                }
            }

            foreach ($request->variations as $variationData) {
                $variationId = $variationData['id'] ?? null;

                $variation = $product->productVariations()->updateOrCreate(
                    ['id' => $variationId],
                    [
                        'product_type_id' => $variationData['product_type_id'],
                        'color_id' => $variationData['color_id'] ?? null,
                        'primary_color_id' => $variationData['primary_color_id'] ?? null,
                        'secondary_color_id' => $variationData['secondary_color_id'] ?? null,
                        'price' => $variationData['price'],
                        'sku' => $variationData['sku'],
                        'image' => $imageMap[$variationId] ?? ($variationId ? $product->productVariations->firstWhere('id', $variationId)?->image : null),
                    ]
                );

                $pivotData = [];

                foreach ($variationData['sizes'] as $sizeData) {
                    $pivotData[$sizeData['id']] = ['stock' => $sizeData['stock']];
                }

                $variation->sizes()->sync($pivotData);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update product: ' . $e->getMessage(), [
                'product_id' => $product->id,
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    public function delete($product)
    {
        $product->delete();
        return $product;
    }

    public function getSearchResults()
    {
        $products = ProductVariation::queryFilter([
            Search::class,
        ])->with(['product.category', 'type'])->get();

        return $products;
    }
}
