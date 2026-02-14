<?php

namespace App\Services;

use App\Events\Product\ProductCreated;
use App\Events\Product\ProductDeleted;
use App\Events\Product\ProductUpdated;
use App\Events\Product\ProductVariationDeleted;
use App\Models\Product;
use App\Models\ProductVariation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\FIlters\Search;
use App\Models\OrderItem;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
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

        $products = $paginate ? $query->paginate(15, ['*'], 'products_page') : $query->get();

        $wishlistIds = Auth::check()
            ? Auth::user()->wishlist()->pluck('product_variation_id')->toArray()
            : [];

        $products->each(function ($variation) use ($wishlistIds) {
            $variation->isInWishlist = in_array($variation->id, $wishlistIds);
        });

        return $products;
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

            DB::afterCommit(fn () => event(
                new ProductCreated($product->id)
            ));

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
            'productVariations.sizes.sizeLabels',
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

        if (Auth::check()) {
            $activeVariation->isInWishlist = Auth::user()
                ->wishlist()
                ->where('product_variation_id', $activeVariation->id)
                ->exists();
        } else {
            $activeVariation->isInWishlist = false;
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
            $incomingVariationIds = collect($request->variations)->pluck('id')->filter()->toArray();

            $variationsToDelete = array_diff($existingVariationIds, $incomingVariationIds);

            $deletedImagePaths = $product->productVariations()
                ->whereIn('id', $variationsToDelete)
                ->pluck('image')
                ->filter()
                ->toArray();

            $product->productVariations()->whereIn('id', $variationsToDelete)->delete();

            foreach ($request->variations as $vIndex => $variationData) {
                $variationId = $variationData['id'] ?? null;
                $imageToStore = $variationData['image'] ?? null;

                if (
                    isset($variationData['image']) &&
                    $variationData['image'] instanceof UploadedFile
                    ) {
                    $storedPath = $variationData['image']->store('products', 'public');
                    $imageToStore = Storage::url($storedPath);
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

                $pivotData = collect($variationData['sizes'] ?? [])
                    ->mapWithKeys(fn ($s) => [$s['id'] => ['stock' => $s['stock']]])
                    ->toArray();

                $variation->sizes()->sync($pivotData);
            }

            DB::afterCommit(fn () => event(
                new ProductUpdated($product, $deletedImagePaths)
            ));

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            logger()->error('Product update failed: ' . $e->getMessage(), ['exception' => $e]);
            throw new Exception(
                'Failed to update product. Reason: ' . $e->getMessage(),
            );
        }
    }

    public function delete($product)
    {
        $hasOrders = OrderItem::whereIn(
            'product_variation_id',
            $product->productVariations()->pluck('id')
        )->exists();

        if ($hasOrders) {
            return false;
        }

        DB::transaction(function () use ($product) {
            $imagePaths = $product->productVariations()
                ->pluck('image')
                ->filter()
                ->toArray();

            $product->delete();

            DB::afterCommit(fn () => event(
                new ProductDeleted($imagePaths)
            ));
        });

        return true;
    }

    public function deleteVariation($variation)
    {
        $hasOrders = OrderItem::where(
            'product_variation_id',
            $variation->id
        )->exists();

        if ($hasOrders) {
            return false;
        }

        $product = $variation->product;

        DB::transaction(function () use ($variation, $product) {
            if ($product->productVariations()->count() <= 1) {
                $imagePaths = $product->productVariations()
                    ->pluck('image')
                    ->filter()
                    ->toArray();

                $product->delete();

                DB::afterCommit(fn () => event(
                    new ProductDeleted($imagePaths)
                ));
            } else {
                $imagePath = $variation->image;

                $variation->delete();

                DB::afterCommit(fn () => event(
                    new ProductVariationDeleted($imagePath)
                ));
            }
        });

        return true;
    }

    public function getSearchResults()
    {
        $products = ProductVariation::queryFilter([
            Search::class,
        ])->with(['product.category', 'type'])->get();

        $wishlistIds = Auth::check()
            ? Auth::user()->wishlist()->pluck('product_variation_id')->toArray()
            : [];

        $products->each(function ($variation) use ($wishlistIds) {
            $variation->isInWishlist = in_array($variation->id, $wishlistIds);
        });

        return $products;
    }
}
