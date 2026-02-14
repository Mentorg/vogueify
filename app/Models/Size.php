<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Size extends Model
{
    public $timestamps = false;

    public function productVariations()
    {
        return $this->belongsToMany(ProductVariation::class, 'product_variation_size')
            ->withPivot('stock');
    }

    public function sizeLabels()
    {
        return $this->hasMany(SizeLabel::class);
    }
}
