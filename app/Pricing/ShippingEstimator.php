<?php

namespace App\Pricing;

use Illuminate\Support\Collection;

class ShippingEstimator
{
    public function calculate(Collection $items, ?array $destination = null): float
    {
        $baseRate = 5.00;

        $totalWeight = $items->sum(function ($item) {
            $weight = is_array($item)
                ? $item['weight'] ?? 0
                : $item->weight ?? 0;

            $quantity = is_array($item)
                ? $item['quantity']
                : $item->quantity;

            return $weight * $quantity;
        });

        if ($destination && isset($destination['country_id'])) {
            if ($destination['country_id'] !== config('app.default_country_id', 1)) {
                $baseRate += 2.00;
            }
        }

        if ($totalWeight > 10) {
            $baseRate += 3.00;
        }

        return round($baseRate, 2);
    }
}
