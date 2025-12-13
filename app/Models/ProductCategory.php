<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    /** @use HasFactory<\Database\Factories\CategoryFactory> */
    use HasFactory;

    public $timestamps = false;

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function productTypes()
    {
        return $this->hasMany(ProductType::class, 'category_id');
    }

    public function coupons()
    {
        return $this->belongsToMany(
            Coupon::class,
            'coupon_product_category',
            'product_category_id',
            'coupon_id'
        )->withTimestamps();
    }
}
