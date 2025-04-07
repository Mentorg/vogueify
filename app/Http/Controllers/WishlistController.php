<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WishlistController extends Controller
{
    public function index(Request $request)
    {
        $wishlist = $request->user()->wishlist()->with(['product.productVariations'])->orderBy('created_at', 'desc')->get();

        return Inertia::render('Wishlist', [
            'wishlist' => $wishlist
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $exists = Wishlist::where('user_id', $request->user()->id)
            ->where('product_id', $request->product_id)
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'This product is already in your wishlist');
        }

        Wishlist::create(
            ['user_id' => $request->user()->id, 'product_id' => $request->product_id]
        );

        return redirect()->back()->with('success', 'Product added to wishlist.');
    }

    public function destroy($id)
    {
        Wishlist::where('id', $id)->where('user_id', request()->user()->id)->delete();
        return redirect()->back()->with('success', 'Product removed from wishlist.');
    }
}
