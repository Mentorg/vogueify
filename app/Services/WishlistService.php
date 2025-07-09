<?php

namespace App\Services;

use App\Models\Wishlist;
use Illuminate\Validation\ValidationException;

class WishlistService
{
    public function getWishlist($request)
    {
        return $request->user()->wishlist()->with(['productVariation.product'])->orderBy('created_at', 'desc')->get();
    }

    public function create($request)
    {
        $request->validate([
            'product_variation_id' => 'required|exists:product_variations,id'
        ]);

        $exists = Wishlist::where('user_id', $request->user()->id)
            ->where('product_variation_id', $request->product_variation_id)
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'product_variation_id' => 'This product is already in your wishlist'
            ]);
        }

        Wishlist::create([
            'user_id' => $request->user()->id,
            'product_variation_id' => $request->product_variation_id
        ]);
    }

    public function delete($id)
    {
        return Wishlist::where('id', $id)->where('user_id', request()->user()->id)->delete();
    }
}
