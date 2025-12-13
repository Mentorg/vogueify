<?php

namespace App\Pricing;

use App\Models\Coupon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class CouponService
{
    public function apply(Coupon $coupon, Collection $items): float
    {
        $user = Auth::user();

        if (!$user) {
            return 0.00;
        }

        $restrictsToVariations = $coupon->productVariations()->exists();
        $restrictsToProducts   = $coupon->products()->exists();
        $restrictsToCategories = $coupon->categories()->exists();

        $eligibleItems = $items->filter(function ($item) use (
            $coupon,
            $restrictsToProducts,
            $restrictsToCategories,
            $restrictsToVariations
        ) {
            $variation  = $item->productVariation ?? null;
            $product    = $variation?->product;
            $category   = $product?->category;

            if (!$restrictsToVariations && !$restrictsToProducts && !$restrictsToCategories) {
                return true;
            }

            if ($restrictsToVariations && $coupon->productVariations->contains($variation?->id)) {
                return true;
            }

            if ($restrictsToProducts && $coupon->products->contains($product?->id)) {
                return true;
            }

            if ($restrictsToCategories && $coupon->categories->contains($category?->id)) {
                return true;
            }

            return false;
        });

        $eligibleTotal = $eligibleItems->sum(function ($item) {
            return $item->price_at_time * $item->quantity;
        });

        if ($eligibleTotal <= 0) {
            return 0.00;
        }

        return match ($coupon->type) {
            'fixed'         => min($eligibleTotal, $coupon->value),
            'percentage'    => round($eligibleTotal * ($coupon->value / 100), 2),
            default         => 0.00,
        };
    }
}
