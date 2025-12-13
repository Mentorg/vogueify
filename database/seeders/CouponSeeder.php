<?php

namespace Database\Seeders;

use App\Models\Coupon;
use App\Models\ProductVariation;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::where('role', 'customer')->take(3)->get()->values();
        $variations = ProductVariation::take(15)->get()->values();

        if ($users->count() < 3 || $variations->count() < 6) {
            $this->command->warn('Skipping CouponSeeder: not enough users or product variations found.');
            return;
        }

        $welcomeCoupon = Coupon::create([
            'code' => 'WELCOME10',
            'couponType' => 'variations',
            'type' => 'percentage',
            'value' => 10,
            'status' => 'active',
            'max_uses' => 75,
            'max_uses_per_user' => 75,
            'starts_at' => now(),
            'expires_at' => now()->addMonth(),
        ]);

        $welcomeCoupon->productVariations()->attach($variations->slice(0, 5)->pluck('id'));

        $exclusiveCoupon = Coupon::create([
            'code' => 'EXCLUSIVE5',
            'couponType' => 'variations',
            'type' => 'fixed',
            'value' => 5.00,
            'status' => 'active',
            'max_uses' => 50,
            'max_uses_per_user' => 50,
            'starts_at' => now(),
            'expires_at' => now()->addWeeks(2),
        ]);

        $exclusiveCoupon->productVariations()->attach($variations->slice(5, 5)->pluck('id'));

        $vipCoupon = Coupon::create([
            'code' => 'VIP15',
            'couponType' => 'variations',
            'type' => 'percentage',
            'value' => 15,
            'status' => 'active',
            'max_uses' => 25,
            'max_uses_per_user' => 25,
            'starts_at' => now(),
            'expires_at' => now()->addWeek(4),
        ]);

        $vipCoupon->productVariations()->attach($variations->slice(10, 5)->pluck('id'));

        $welcomeCoupon->users()->attach($users[0]->id);
        $exclusiveCoupon->users()->attach($users[1]->id);
        $vipCoupon->users()->attach($users[2]->id);
    }
}
