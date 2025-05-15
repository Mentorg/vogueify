<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductVariation;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index()
    {
        return Inertia::render('Home', [
            'categories' => $this->getCategories(),
            'latestWomenBags' => $this->getLatestWomenBags(),
        ]);
    }

    public function search()
    {
        return Inertia::render('Search', [
            'categories' => $this->getCategories(),
            'womenSeasonalBags' => $this->getWomenSeasonalBags(),
            'menSeasonalBags' => $this->getMenSeasonalBags(),
        ]);
    }

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

        return $products;
    }
}
