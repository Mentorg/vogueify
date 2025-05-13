<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
    protected $fillable = ['name', 'hex_code'];

    public function productVariations()
    {
        return $this->hasMany(ProductVariation::class, 'color_id');
    }

    public function primaryVariations()
    {
        return $this->hasMany(ProductVariation::class, 'primary_color_id');
    }

    public function secondaryVariations()
    {
        return $this->hasMany(ProductVariation::class, 'secondary_color_id');
    }
}
