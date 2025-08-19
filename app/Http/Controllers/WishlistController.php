<?php

namespace App\Http\Controllers;

use App\Models\ProductVariation;
use App\Services\WishlistService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WishlistController extends Controller
{
    protected $wishlistService;

    public function __construct(WishlistService $wishlistService)
    {
        $this->wishlistService = $wishlistService;
    }

    public function index(Request $request)
    {
        return Inertia::render('Wishlist', [
            'wishlist' => $this->wishlistService->getWishlist($request),
        ]);
    }

    public function store(Request $request)
    {
        $this->wishlistService->create($request);

        return redirect()->back()->with('success', 'Product added to wishlist.');
    }

    public function destroy($id)
    {
        $this->wishlistService->delete($id);

        return redirect()->back()->with('success', 'Product removed from wishlist.');
    }

    public function toggle(ProductVariation $productVariation)
    {
        $this->wishlistService->toggle($productVariation);

        return back();
    }
}
