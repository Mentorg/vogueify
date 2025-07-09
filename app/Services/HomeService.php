<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductVariation;

class HomeService
{
    public function getCategories()
    {
        return ProductCategory::all();
    }

    public function getLatestWomenBags()
    {
        return Product::with('productVariations')
            ->whereHas('category', function ($query) {
                $query->where('name', 'bags');
            })
            ->where('gender', 'women')
            ->latest()
            ->take(4)
            ->get();
    }

    public function getWomenSeasonalBags()
    {
        return ProductVariation::with(['product.category'])
            ->whereHas('product', function ($query) {
                $query->where('gender', 'women')
                    ->whereHas('category', function ($q) {
                        $q->where('name', 'bags');
                    });
            })
            ->latest()
            ->take(6)
            ->get();
    }

    public function getMenSeasonalBags()
    {
        return ProductVariation::with(['product.category'])
            ->whereHas('product', function ($query) {
                $query->where('gender', 'men')
                    ->whereHas('category', function ($q) {
                        $q->where('name', 'bags');
                    });
            })
            ->latest()
            ->take(6)
            ->get();
    }
}
