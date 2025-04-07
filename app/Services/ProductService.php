<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\FIlters\Search;
use Exception;

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
            throw new Exception('Failed to store the product' . $e->getMessage(), $e->getCode(), $e);
        }
    }

    public function getSearchResults()
    {
        $products = Product::queryFilter([
            Search::class,
        ])->get();

        return $products->load('productVariations', 'category');
    }
}
