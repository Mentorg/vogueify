<?php

namespace App\Pricing;

use App\Models\Coupon;
use Illuminate\Support\Collection;

class PricingService
{
    protected $couponService;
    protected $taxRuleService;
    protected $shippingEstimator;

    public function __construct(CouponService $couponService, TaxRuleService $taxRuleService, ShippingEstimator $shippingEstimator)
    {
        $this->couponService = $couponService;
        $this->taxRuleService = $taxRuleService;
        $this->shippingEstimator = $shippingEstimator;
    }

    public function calculate(Collection $items, array $options = [])
    {
        $countryCode = $options['countryCode'] ?? null;
        $state = $options['state'] ?? null;
        $coupon = $options['coupon'] ?? null;
        $destination = $options['destination'] ?? null;

        $subtotal = $this->calculateSubtotal($items);

        $shipping = $this->shippingEstimator->calculate(
            $items,
            [
                'country_id' => $destination['country_id'] ?? config('app.default_country_id', 1),
                'postcode'   => $destination['postcode'] ?? null,
            ]
        );

        $isShippingTaxable = $this->taxRuleService->isShippingTaxable($countryCode, $state);

        $taxRate = $this->taxRuleService->getTaxRate($countryCode, $state);

        $discount = 0;
        if ($coupon instanceof Coupon) {
            $discount = $this->couponService->apply($coupon, $items);
        }

        $subtotalAfterDiscount = max(0, $subtotal - $discount);

        $taxBase = $subtotalAfterDiscount + ($isShippingTaxable ? $shipping : 0);
        $tax = $this->calculateTax($taxBase, $taxRate);

        $total = $subtotalAfterDiscount + $shipping + $tax;

        return $this->formatTotals([
            'subtotal' => $subtotal,
            'discount' => $discount,
            'shipping' => $shipping,
            'tax' => $tax,
            'total' => $total,
            'isShippingTaxable' => $isShippingTaxable,
        ]);
    }

    public function calculateSubtotal(Collection $items): float
    {
        return $items->sum(function ($item) {
            $price = is_array($item) ? $item['price_at_time'] : $item->price_at_time;
            $quantity = is_array($item) ? $item['quantity'] : $item->quantity;

            return $price * $quantity;
        });
    }

    public function calculateTax(float $amount, float $rate): float
    {
        return round($amount * $rate, 2);
    }

    public function formatTotals(array $amounts): array
    {
        return collect($amounts)->map(fn ($value) => round($value, 2))->toArray();
    }
}
