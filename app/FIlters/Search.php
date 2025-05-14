<?php

namespace App\FIlters;

use Illuminate\Database\Eloquent\Builder;
use Samushi\QueryFilter\Filter;

class Search extends Filter
{
    /**
     * Search results using whereLike
     *
     * @param Builder $builder
     * @return Builder
     */
    protected function applyFilter(Builder $builder): Builder
    {
        return $builder->whereLike(
            [
                'product.name',
                'product.gender',
                'sku'
            ],
            $this->getValue()
        )
        ->orWhereHas('product.category', function($q) {
            $q->where('name', 'like', '%' . $this->getValue() . '%');
        })
        ->orWhereHas('color', function($q) {
            $q->where('name', 'like', '%' . $this->getValue() . '%');
        })
        ->orWhereHas('primaryColor', function($q) {
            $q->where('name', 'like', '%' . $this->getValue() . '%');
        })
        ->orWhereHas('secondaryColor', function($q) {
            $q->where('name', 'like', '%' . $this->getValue() . '%');
        })
        ->orWhereHas('type', function($q) {
            $q->where('label', 'like', '%' . $this->getValue() . '%');
        })
        ->orWhereHas('color', function($q) {
            $q->where('name', 'like', '%' . $this->getValue() . '%')
            ->orWhereNull('name');
        });
    }
}
