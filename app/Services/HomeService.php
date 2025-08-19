<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductVariation;
use Illuminate\Support\Facades\Auth;

class HomeService
{
    public function getCategories()
    {
        return ProductCategory::all();
    }

    public function getLatestWomenBags()
    {
        $products = Product::with('productVariations')
            ->whereHas('category', function ($query) {
                $query->where('name', 'bags');
            })
            ->where('gender', 'women')
            ->latest()
            ->take(4)
            ->get();

        $wishlistIds = Auth::check()
            ? Auth::user()->wishlist()->pluck('product_variation_id')->toArray()
            : [];

        $products->each(function ($variation) use ($wishlistIds) {
            $variation->isInWishlist = in_array($variation->id, $wishlistIds);
        });

        return $products;
    }

    public function getWomenSeasonalBags()
    {
        $products = ProductVariation::with(['product.category'])
            ->whereHas('product', function ($query) {
                $query->where('gender', 'women')
                    ->whereHas('category', function ($q) {
                        $q->where('name', 'bags');
                    });
            })
            ->latest()
            ->take(6)
            ->get();

        $wishlistIds = Auth::check()
        ? Auth::user()->wishlist()->pluck('product_variation_id')->toArray()
        : [];

        $products->each(function ($variation) use ($wishlistIds) {
            $variation->isInWishlist = in_array($variation->id, $wishlistIds);
        });

        return $products;
    }

    public function getMenSeasonalBags()
    {
        $products = ProductVariation::with(['product.category'])
            ->whereHas('product', function ($query) {
                $query->where('gender', 'men')
                    ->whereHas('category', function ($q) {
                        $q->where('name', 'bags');
                    });
            })
            ->latest()
            ->take(6)
            ->get();
            $wishlistIds = Auth::check()
            ? Auth::user()->wishlist()->pluck('product_variation_id')->toArray()
            : [];

        $products->each(function ($variation) use ($wishlistIds) {
            $variation->isInWishlist = in_array($variation->id, $wishlistIds);
        });

        return $products;
    }
}
