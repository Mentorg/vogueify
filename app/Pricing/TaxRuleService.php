<?php

namespace App\Pricing;

class TaxRuleService
{
    public function getTaxRate(?string $countryCode = null, ?string $state = null): float
    {
        $taxRates = [
            'US' => [
                'CA' => 0.0825,
                'NY' => 0.088,
                'default' => 0.07
            ],
            'UK' => ['default' => 0.20],
            'DE' => ['default' => 0.19],
            'FR' => ['default' => 0.20],
            'IT' => ['default' => 0.22],
            'ES' => ['default' => 0.21],
            'CH' => ['default' => 0.081],
            'AT' => ['default' => 0.20],
            'PL' => ['default' => 0.23],
            'SE' => ['default' => 0.25],
            'NO' => ['default' => 0.25],
            'FI' => ['default' => 0.24],
            'IE' => ['default' => 0.23],
            'NL' => ['default' => 0.21],
            'BE' => ['default' => 0.21],
            'DK' => ['default' => 0.25],
            'CZ' => ['default' => 0.21],
            'XK' => ['default' => 0.18],
        ];

        $countryCode = strtoupper($countryCode ?? '');
        $state = strtoupper($state ?? '');

        $country = $taxRates[$countryCode] ?? ['default' => 0.10];

        return $country[$state] ?? $country['default'];
    }

    public function isShippingTaxable(?string $countryCode = null, ?string $state = null): bool
    {
        $rules = [
            'US' => [
                'CA' => true,
                'NY' => true,
                'default' => false,
            ],
            'UK' => ['default' => true],
            'DE' => ['default' => true],
            'FR' => ['default' => true],
            'IT' => ['default' => true],
            'ES' => ['default' => true],
            'CH' => ['default' => true],
            'AT' => ['default' => true],
            'PL' => ['default' => true],
            'SE' => ['default' => true],
            'NO' => ['default' => true],
            'FI' => ['default' => true],
            'IE' => ['default' => true],
            'NL' => ['default' => true],
            'BE' => ['default' => true],
            'DK' => ['default' => true],
            'CZ' => ['default' => true],
            'XK' => ['default' => true],
        ];

        $countryCode = strtoupper($countryCode ?? '');
        $state = strtoupper($state ?? '');

        $country = $rules[$countryCode] ?? ['default' => false];

        return $country[$state] ?? $country['default'];
    }
}
