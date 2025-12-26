<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductVariation;
use App\Models\User;
use App\Notifications\Coupon\CouponSelectedUsersNotification;
use App\Pricing\Exceptions\InvalidCouponException;
use App\Pricing\Validators\CouponValidator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CouponService
{
    private $validator;

    public function __construct(CouponValidator $validator)
    {
        $this->validator = $validator;
    }

    private function calculateDiscountedPrice($coupon, $originalPrice)
    {
        return match ($coupon->type) {
            'fixed' => max(0, $originalPrice - $coupon->value),
            'percentage' => round($originalPrice * (1 - $coupon->value / 100), 2),
            default => $originalPrice,
        };
    }

    private function normalizeItem ($item)
    {
        if (is_array($item)) {
            $variation = $item['product_variation'] ?? $item['productVariation'] ?? null;
            return [
                'variation_id' => (int) ($item['product_variation_id'] ?? $variation['id'] ?? 0),
                'product_id'   => (int) ($variation['product']['id'] ?? $variation['product_id'] ?? 0),
                'category_id'  => (int) ($variation['product']['category_id'] ?? 0),
                'price'        => (float) ($item['price_at_time'] ?? 0),
                'qty'          => (int) ($item['quantity'] ?? 1),
            ];
        }

        $variation = $item->product_variation ?? $item->productVariation;

        return [
            'variation_id'  => (int) ($item->product_variation_id ?? $variation->id ?? 0),
            'product_id'    => (int) (optional($variation->product)->id),
            'category_id'   => (int) (optional($variation->product)->category_id),
            'price'         => (float) ($item->price_at_time ?? 0),
            'qty'           => (int) ($item->quantity ?? 1),
        ];
    }

    private function computeDiscount ($price, $qty, $type, $value)
    {
        return match ($type) {
            'percentage' => $price * $qty * ($value / 100),
            'fixed' => min($value * $qty, $price * $qty),
            default => 0,
        };
    }

    private function handleProductVariationCoupon ($coupon, $items, $discountType, $discountValue)
    {
        $discount = 0.0;

        $eligibleIds = $coupon->productVariations()
            ->pluck('product_variations.id')
            ->map(fn ($id) => (int)$id)
            ->toArray();

        foreach ($items as $item) {
            if (in_array($item['variation_id'], $eligibleIds, true)) {
                $discount += $this->computeDiscount($item['price'], $item['qty'], $discountType, $discountValue);
            }
        }

        return $discount;
    }

    private function handleProductCoupon ($coupon, $items, $discountType, $discountValue)
    {
        $discount = 0.0;

        $eligibleIds = $coupon->products()
            ->pluck('products.id')
            ->map(fn($id) => (int) $id)
            ->toArray();

        foreach ($items as $item) {
            if (in_array($item['product_id'], $eligibleIds, true)) {
                $discount += $this->computeDiscount($item['price'], $item['qty'], $discountType, $discountValue);
            }
        }

        return $discount;
    }

    private function handleCategoryCoupon ($coupon, $items, $discountType, $discountValue)
    {
        $discount = 0.0;

        $eligibleCategoryIds = $coupon->categories()
            ->pluck('product_categories.id')
            ->map(fn($id) => (int) $id)
            ->toArray();

        foreach ($items as $item) {
            if (in_array($item['category_id'], $eligibleCategoryIds, true)) {
                $discount += $this->computeDiscount($item['price'], $item['qty'], $discountType, $discountValue);
            }
        }

        return $discount;
    }

    public function getCoupons()
    {
        $coupons = Coupon::orderBy('created_at', 'desc')->paginate(15, ['*'], 'coupons_page');
        $categories = ProductCategory::all();
        $products = Product::all();
        $productVariations = ProductVariation::with('product')->get();
        $users = User::all();

        $coupons->load([
            'users',
            'orders',
            'productVariations.product.category',
            'products.productVariations',
            'categories',
            'author',
            'updatedBy',
        ]);

        return [
            $coupons,
            $categories,
            $products,
            $productVariations,
            $users,
        ];
    }

    public function getCoupon($coupon)
    {
        $coupon->load([
            'users',
            'orders',
            'productVariations.product.category',
            'products.productVariations',
            'products.category',
            'categories',
            'author',
            'updatedBy',
        ]);

        $coupon->productVariations->transform(function ($variation) use ($coupon) {
            $originalPrice = $variation->price;
            $discountedPrice = $this->calculateDiscountedPrice($coupon, $originalPrice);

            $variation->original_price = round($originalPrice, 2);
            $variation->discounted_price = round($discountedPrice, 2);
            $variation->discount_amount = round($originalPrice - $discountedPrice, 2);

            return $variation;
        });

        return $coupon;
    }

    public function create($validated)
    {
        $couponData = [
            'couponType' => $validated['couponType'],
            'code' => $validated['code'],
            'type' => $validated['type'],
            'value' => $validated['value'],
            'starts_at' => $validated['starts_at'],
            'expires_at' => $validated['expires_at'],
            'status' => $validated['status'] ?? 'active',
            'max_uses' => $validated['max_uses'],
            'max_uses_per_user' => $validated['max_uses_per_user'],
            'created_by' => Auth::id(),
            'categories' => $validated['categories'] ?? [],
            'products' => $validated['products'] ?? [],
            'productVariations' => $validated['productVariations'] ?? [],
            'users' => $validated['users'] ?? []
        ];

        $coupon = Coupon::create($couponData);

        if (!empty($validated['categories'])) {
            $coupon->categories()->attach($validated['categories']);
        }

        if (!empty($validated['products'])) {
            $coupon->products()->attach($validated['products']);
        }

        if (!empty($validated['productVariations'])) {
            $coupon->productVariations()->attach($validated['productVariations']);
        }

        if (!empty($validated['users'])) {
            $coupon->users()->attach($validated['users']);
        }

        if (!empty($validated['sendNotification']) && $validated['sendNotification'] && $validated['status'] === 'active') {
            $this->sendUserNotifications($coupon);
        }
    }

    public function update($coupon, $validated)
    {
        $coupon->update([
            'couponType' => $validated['couponType'],
            'code' => $validated['code'],
            'type' => $validated['type'],
            'value' => $validated['value'],
            'starts_at' => $validated['starts_at'],
            'expires_at' => $validated['expires_at'],
            'status' => $validated['status'],
            'max_uses' => $validated['max_uses'],
            'max_uses_per_user' => $validated['max_uses_per_user'],
            'updated_by' => Auth::id(),
            'categories' => $validated['categories'] ?? [],
            'products' => $validated['products'] ?? [],
            'productVariations' => $validated['productVariations'] ?? [],
            'users' => $validated['users'] ?? []
        ]);
    }

    public function delete($coupon)
    {
        $coupon->delete();
    }

    public function apply($coupon, $items)
    {
        if ($items->isEmpty()) {
            return 0.0;
        }

        $normalizedItems = $items->map(fn($item) => $this->normalizeItem($item));

        $discountType = $coupon->type;
        $discountValue = (float) $coupon->value;
        $discount = 0.0;

        switch ($coupon->couponType) {
            case 'variations':
                $discount = $this->handleProductVariationCoupon($coupon, $normalizedItems, $discountType, $discountValue);
                break;

            case 'products':
                $discount = $this->handleProductCoupon($coupon, $normalizedItems, $discountType, $discountValue);
                break;

            case 'categories':
                $discount = $this->handleCategoryCoupon($coupon, $normalizedItems, $discountType, $discountValue);
                break;

            default:
                Log::warning("Unknown coupon type '{$coupon->couponType}'");
        }

        return round($discount, 2);
    }

    public function validateAndApply($code, $user)
    {
        $coupon = $this->validator->validate($code, $user);

        $cartItems = $user->cart
            ? $user->cart->cartItems()->with('productVariation.product.category')->get()
            : collect();

        $discount = $this->apply($coupon, $cartItems);

        if ($discount <= 0) {
            throw new InvalidCouponException('no_discount', "Coupon '{$coupon->code}' does not apply to your cart!");
        }

        session(['applied_coupon' => $code]);

        return $discount;
    }

    public function remove()
    {
        session()->forget('applied_coupon');
    }

    public function updateStatus($coupon, $status)
    {
        $coupon->update(['status' => $status]);
    }

    public function sendUserNotifications($coupon)
    {
        $coupon->load([
            'users',
            'categories',
            'products',
            'productVariations.product',
            'productVariations.color',
            'productVariations.primaryColor',
            'productVariations.secondaryColor',
            'productVariations.type',
        ]);

        $users = $coupon->users;

        if ($coupon->status !== 'active' || $users->isEmpty()) {
            return;
        }

        foreach ($users as $user) {
            $user->notify(new CouponSelectedUsersNotification($coupon));
        }
    }
}
