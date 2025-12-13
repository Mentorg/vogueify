<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'couponType',
        'type',
        'value',
        'starts_at',
        'expires_at',
        'status',
        'max_uses',
        'max_uses_per_user',
        'uses',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('uses')
            ->withTimestamps();
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function categories()
    {
        return $this->belongsToMany(
            ProductCategory::class,
            'coupon_product_category',
            'coupon_id',
            'product_category_id'
        );
    }

    public function products()
    {
        return $this->belongsToMany(
            Product::class,
            'coupon_product',
            'coupon_id',
            'product_id'
        );
    }

    public function productVariations()
    {
        return $this->belongsToMany(
            ProductVariation::class,
            'coupon_product_variation',
            'coupon_id',
            'product_variation_id'
        )->withTimestamps();
    }


    public function variations()
    {
        return $this->belongsToMany(
            ProductVariation::class,
            'coupon_product_variation',
            'coupon_id',
            'product_variation_id'
        );
    }

    public function getUserUsageCount(?User $user): int
    {
        if (!$user) {
            return 0;
        }

        return $this->users()
            ->where('user_id', $user->id)
            ->first()?->pivot->uses ?? 0;
    }

    public function incrementUsageForUser(User $user): void
    {
        $pivot = $this->users()->where('user_id', $user->id)->first()?->pivot;

        if ($pivot) {
            $pivot->increment('uses');
        } else {
            $this->users()->attach($user->id, ['uses' => 1]);
        }
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
