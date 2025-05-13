<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductType extends Model
{
    use HasFactory;

    protected $fillable = ['type', 'label', 'category_id'];

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }
}
