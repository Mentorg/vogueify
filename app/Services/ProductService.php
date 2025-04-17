<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\FIlters\Search;
use Exception;
use Illuminate\Support\Facades\Log;

class ProductService
{
    public function getProducts($request)
    {
        $query = Product::query();

        if ($request->has('gender')) {
            $query->where('gender', $request->gender);
        }

        if ($request->has('category')) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('name', $request->category);
            });
        }

        if ($request->has('type')) {
            $query->whereHas('productVariations', function($q) use ($request) {
                $q->where('type', $request->type);
            });
        }

        return $query->with('productVariations', 'category')->get();
    }

    public function getProduct($product)
    {
        return $product->load('productVariations');
    }

    public function create(array $validated, $request)
    {
        DB::beginTransaction();

        try {
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('products', 'public');
                $imagePath = Storage::url($imagePath);
            } else {
                $imagePath = null;
            }

            $product = Product::create([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'features' => $validated['features'],
                'gender' => $validated['gender'],
                'category_id' => $validated['category_id'],
            ]);

            ProductVariation::create([
                'product_id' => $product->id,
                'image' => $imagePath,
                'color' => $validated['color'],
                'type' => $validated['type'],
                'price' => $validated['price'],
                'stock' => $validated['stock'],
                'sku' => $validated['sku']
            ]);

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

    public function update(Product $product, $request, $image = null)
    {
        $validated = $request->validated();

        DB::beginTransaction();

        try {
            $variation = $product->productVariations->first();

            if (!$variation) {
                throw new Exception('Product variation not found for product ID: ' . $product->id);
            }

            if ($request->hasFile('image')) {
                if (!empty($variation->image)) {
                    $oldImagePath = str_replace('/storage/', '', $variation->image);
                    Storage::disk('public')->delete($oldImagePath);
                }

                $imagePath = $request->file('image')->store('products', 'public');
                $imagePath = Storage::url($imagePath);
            } else {
                $imagePath = $variation->image;
            }

            $product->update([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'features' => $validated['features'],
                'gender' => $validated['gender'],
                'category_id' => $validated['category_id'],
            ]);

            $variation->update([
                'image' => $imagePath,
                'color' => $validated['color'],
                'type' => $validated['type'],
                'price' => $validated['price'],
                'stock' => $validated['stock'],
                'sku' => $validated['sku'],
            ]);

            DB::commit();

            return $product->load('productVariations');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to update the product: ' . $e->getMessage(), [
                'product_id' => $product->id,
                'exception' => $e
            ]);
            throw new Exception('Failed to update the product: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }

    public function delete($product)
    {
        $product->delete();
        return $product;
    }

    public function getSearchResults()
    {
        $products = Product::queryFilter([
            Search::class,
        ])->get();

        return $products->load('productVariations', 'category');
    }
}
