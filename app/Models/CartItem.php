<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = [
        'cart_id',
        'product_variation_id',
        'size_id',
        'quantity',
        'price_at_time'
    ];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function productVariation()
    {
        return $this->belongsTo(ProductVariation::class);
    }

    public function size()
    {
        return $this->belongsTo(Size::class);
    }
}
