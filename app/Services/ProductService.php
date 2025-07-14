<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\FIlters\Search;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;

class ProductService
{
    public function getProducts($request, bool $paginate = false)
    {
        $query = ProductVariation::with(['product.category', 'sizes', 'type']);

        if ($request->has('gender')) {
            $query->whereHas('product', fn($q) => $q->where('gender', $request->gender));
        }

        if ($request->has('category')) {
            $query->whereHas('product.category', fn($q) => $q->where('name', $request->category));
        }

        if ($request->has('type')) {
            $query->whereHas('type', fn($q) => $q->where('type', $request->type));
        }

        return $paginate ? $query->paginate(15) : $query->get();
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
                    'stock' => $variationData['stock'],
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
            );
        }
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

        $variations = $product->productVariations;

        if ($variation) {
            $activeVariation = $variations->firstWhere('sku', $variation);

            if (!$activeVariation) {
                throw ValidationException::withMessages([
                    'variation' => 'The selected variation is invalid for this product.',
                ]);
            }
        } else {
            $activeVariation = $variations->first();
        }

        return [
            'product' => $product,
            'activeVariation' => $activeVariation,
        ];
    }

    public function update($request, $product): void
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


            $existingVariationIds = $product->productVariations->pluck('id')->toArray();

            $incomingVariationIds = collect($request->variations)
                                    ->pluck('id')
                                    ->filter()
                                    ->toArray();

            $variationsToDelete = array_diff($existingVariationIds, $incomingVariationIds);

            if (!empty($variationsToDelete)) {
                $variationsWithImagesToDelete = $product->productVariations()->whereIn('id', $variationsToDelete)->get();
                foreach ($variationsWithImagesToDelete as $var) {
                    if ($var->image) {
                        $oldPath = str_replace('/storage/', '', $var->image);
                        if (Storage::disk('public')->exists($oldPath)) {
                            Storage::disk('public')->delete($oldPath);
                        }
                    }
                }
                $product->productVariations()->whereIn('id', $variationsToDelete)->delete();
            }

            foreach ($request->variations as $vIndex => $variationData) {
                $variationId = $variationData['id'] ?? null;
                $imageToStore = null;

                if (isset($variationData['image']) && $variationData['image'] instanceof UploadedFile) {
                    $file = $variationData['image'];
                    $storedPath = $file->store('products', 'public');
                    $imageToStore = Storage::url($storedPath);

                    if ($variationId) {
                        $existingVariation = $product->productVariations->firstWhere('id', $variationId);
                        if ($existingVariation && $existingVariation->image) {
                            $oldPath = str_replace('/storage/', '', $existingVariation->image);
                            Storage::disk('public')->delete($oldPath);
                        }
                    }
                } elseif (isset($variationData['image']) && is_string($variationData['image'])) {
                    $imageToStore = $variationData['image'];
                }

                $variation = $product->productVariations()->updateOrCreate(
                    ['id' => $variationId],
                    [
                        'product_type_id' => $variationData['product_type_id'],
                        'color_id' => $variationData['color_id'] ?? null,
                        'primary_color_id' => $variationData['primary_color_id'] ?? null,
                        'secondary_color_id' => $variationData['secondary_color_id'] ?? null,
                        'price' => $variationData['price'],
                        'sku' => $variationData['sku'],
                        'stock' => $variationData['stock'] ?? null,
                        'image' => $imageToStore,
                    ]
                );

                $pivotData = [];
                foreach ($variationData['sizes'] ?? [] as $sizeData) {
                    $pivotData[$sizeData['id']] = ['stock' => $sizeData['stock']];
                }
                $variation->sizes()->sync($pivotData);
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            logger()->error('Product update failed: ' . $e->getMessage(), ['exception' => $e]);
            throw new Exception(
                'Failed to update product. Reason: ' . $e->getMessage(),
            );
        }
    }

    public function delete(Product $product)
    {
        return tap($product)->delete();
    }

    public function deleteVariation(ProductVariation $variation)
    {
        $product = $variation->product;

        if ($product->productVariations()->count() <= 1) {
            return $this->delete($product);
        }

        return tap($variation)->delete();
    }

    public function getSearchResults()
    {
        $products = ProductVariation::queryFilter([
            Search::class,
        ])->with(['product.category', 'type'])->get();

        return $products;
    }
}
