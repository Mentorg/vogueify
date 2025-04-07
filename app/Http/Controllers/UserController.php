<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    public function getWishlist(Request $request)
    {

        $wishlist = $request->user()->wishlist()->with(['product.productVariations'])->get();

        return Inertia::render('Dashboard', [
            'wishlist' => $wishlist
        ]);
    }
}
