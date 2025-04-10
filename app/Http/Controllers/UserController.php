<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Users', [
            'users' => User::paginate(15)
        ]);
    }

    public function getProducts()
    {
        return Inertia::render('Admin/Products', [
            'products' => Product::with(['productVariations', 'category'])->paginate(15)
        ]);
    }

    public function getWishlist(Request $request)
    {
        $wishlist = $request->user()->wishlist()->with(['product.productVariations'])->get();

        return Inertia::render('Dashboard', [
            'wishlist' => $wishlist
        ]);
    }
}
