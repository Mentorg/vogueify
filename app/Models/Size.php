<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Size extends Model
{
    protected $fillable = ['label'];

    public function productVariations()
    {
        return $this->belongsToMany(ProductVariation::class, 'product_variation_size')
            ->withPivot('stock');
    }
}
