<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariation extends Model
{
    use HasFactory;

    protected $fillable = [
        'image',
        'price',
        'sku',
        'product_id',
        'color_id',
        'primary_color_id',
        'secondary_color_id',
        'product_type_id'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function sizes()
    {
        return $this->belongsToMany(Size::class, 'product_variation_size')
            ->withPivot('stock');
    }

    public function color()
    {
        return $this->belongsTo(Color::class, 'color_id');
    }

    public function primaryColor()
    {
        return $this->belongsTo(Color::class, 'primary_color_id');
    }

    public function secondaryColor()
    {
        return $this->belongsTo(Color::class, 'secondary_color_id');
    }

    public function type()
    {
        return $this->belongsTo(ProductType::class, 'product_type_id');
    }
}
