<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Pricing\Exceptions\InvalidCouponException;
use App\Pricing\PricingService;
use App\Pricing\Validators\CouponValidator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class CartService
{
    protected $pricingService;

    public function __construct(PricingService $pricingService)
    {
        $this->pricingService = $pricingService;
    }

    public function getCartItems(?string $couponCode = null)
    {
        $user = Auth::user();

        if (!$user) {
            abort(404);
        }

        $cart = $user->cart;

        if (!$cart) {
            return collect();
        }

        $cartItems = $cart->cartItems()->with([
            'productVariation.product',
            'productVariation.product.category',
            'productVariation.sizes',
            'size'
        ])->get();

        $wishlistIds = $user->wishlist()->pluck('product_variation_id')->toArray();
        $cartItems->each(function ($item) use ($wishlistIds) {
            $item->productVariation->isInWishlist = in_array($item->productVariation->id, $wishlistIds);
        });

        $address = $user->address;

        if (!$address) {
            Log::warning('No user address found for cart tax calculation.', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);
        }

        $destination = [
            'country_id' => $address?->country_id,
            'postcode' => $address?->postcode,
        ];

        $countryCode = $address?->country?->iso_code;
        $state = $address?->state;

        $couponModel = null;
        $couponCode ??= session('applied_coupon');

        if (is_array($couponCode)) {
            $couponCode = $couponCode['code'] ?? null;
        }

        if ($couponCode) {
            try {
                $couponModel = app(CouponValidator::class)->validate($couponCode, $user);
            } catch (InvalidCouponException $e) {
                session()->forget('applied_coupon');
                $couponModel = null;
            }
        }

        $totals = $this->pricingService->calculate($cartItems, [
            'coupon' => $couponModel,
            'countryCode' => $countryCode,
            'state' => $state,
            'destination' => $destination,
        ]);

        return [
            'cartItems' => $cartItems,
            'coupon'    => $couponModel ? $couponModel->load('productVariations.product.category') : null,
            'subtotal'  => $totals['subtotal'],
            'discount'  => $totals['discount'],
            'shipping'  => $totals['shipping'],
            'tax'       => $totals['tax'],
            'total'     => $totals['total'],
            'isShippingTaxable' => $totals['isShippingTaxable'],
        ];
    }

    public function create($request)
    {
        if (!$request->user()) {
            abort(404);
        }

        $validated = $request->validate([
            'quantity' => "numeric|min:1",
            'price_at_time' => "numeric|min:0.01",
            'cart_id' => "exists:carts,id",
            'product_variation_id' => "exists:product_variations,id",
            'size_id' => "nullable|exists:sizes,id",
        ]);

        $cart = Cart::where('user_id', Auth::id())->first();

        if (!$cart) {
            $cart = Cart::create(['user_id' => Auth::id()]);
        }

        if ($cart->is_locked) {
            throw ValidationException::withMessages([
                'error' => 'You cannot modify the cart until payment is completed.',
            ]);
        }

        $cart_item = $cart->cartItems()->create([
            'quantity' => $validated['quantity'],
            'price_at_time' => $validated['price_at_time'],
            'product_variation_id' => $validated['product_variation_id'],
            'size_id' => $validated['size_id'],
        ]);

        $cart_item->save();
    }

    public function delete($id)
    {
        $cartItem = CartItem::findOrFail($id);

        $cart = $cartItem->cart;

        $cartItem->delete();

        if ($cart->cartItems()->count() === 0) {
            $cart->is_locked = false;
            session()->forget('applied_coupon');
            $cart->save();
        }
    }
}
