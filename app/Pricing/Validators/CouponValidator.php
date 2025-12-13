<?php

namespace App\Pricing\Validators;

use App\Models\Coupon;
use App\Models\User;
use App\Pricing\Exceptions\InvalidCouponException;
use Carbon\Carbon;

class CouponValidator
{
    /**
     * Validate a coupon code and return the Coupon model if valid.
     *
     * @param string $code
     * @param User|null $user
     * @return Coupon
     *
     * @throws InvalidCouponException
     */
    public function validate(string $code, ?User $user = null): Coupon
    {
        $now = Carbon::now();

        if (is_array($code)) {
            $code = $code['code'] ?? null;
        }

        if (!$code || !is_string($code)) {
            throw new InvalidCouponException('invalid', 'Invalid coupon format.');
        }

        $coupon = Coupon::where('code', $code)->first();

        if (!$coupon) {
            throw new InvalidCouponException('not_found', "Coupon code '{$code}' does not exist.");
        }

        if ($coupon->status !== 'active') {
            throw new InvalidCouponException('inactive', "Coupon '{$code}' is not active.");
        }

        if (!$coupon->couponType || !$coupon->value) {
            throw new InvalidCouponException('invalid', "Coupon '{$code}' is invalid (missing discount info).");
        }

        if ($coupon->expires_at && $now->gt(Carbon::parse($coupon->expires_at)->setTimezone(config('app.timezone')))) {
            throw new InvalidCouponException('expired', "Coupon '{$code}' has expired.");
        }

        if ($coupon->max_uses !== null && $coupon->uses >= $coupon->max_uses) {
            throw new InvalidCouponException('max_uses', "Coupon '{$code}' has reached its usage limit.");
        }

        if ($coupon->users()->exists() && $user && !$coupon->users->contains($user->id)) {
            throw new InvalidCouponException('not_eligible', "You are not eligible to use coupon '{$code}'.");
        }

        if ($user && $coupon->max_uses_per_user !== null) {
            $userUsage = $coupon->getUserUsageCount($user);

            if ($userUsage >= $coupon->max_uses_per_user) {
                throw new InvalidCouponException(
                    'max_uses_per_user',
                    "You have already used coupon '{$code}' the maximum number of {$coupon->max_uses_per_user} times."
                );
            }
        }

        return $coupon;
    }
}

