<?php

namespace App\Models;

use Closure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    protected $casts = [
        'features' => 'array',
    ];

    protected $guarded = [];

    public function productVariations()
    {
        return $this->hasMany(ProductVariation::class);
    }

    public function category()
    {
        return $this->belongsTo(ProductCategory::class);
    }

    public function wishlist()
    {
        return $this->hasMany(Wishlist::class);
    }
}
