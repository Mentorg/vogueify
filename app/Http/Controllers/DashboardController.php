<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductVariation;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Inertia\Inertia;

class DashboardController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAll', User::class);

        return Inertia::render('Admin/Overview', [
            'products' => ProductVariation::with(['product.category', 'sizes', 'type'])->paginate(15),
            'categories' => ProductCategory::all(),
            'users' => User::paginate(15)
        ]);
    }

    public function getUsers()
    {
        $this->authorize('viewAll', User::class);

        return Inertia::render('Admin/Users', [
            'users' => User::paginate(15)
        ]);
    }

    public function getProducts()
    {
        $this->authorize('viewAll', Product::class);

        return Inertia::render('Admin/Products', [
            'variations' => ProductVariation::with(['product.category', 'sizes', 'type'])->paginate(15),
            'categories' => ProductCategory::all()
        ]);
    }
}
