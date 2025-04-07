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
                'name',
                'gender',
                'productVariations.color',
                'productVariations.type',
                'productVariations.sku',
                'category.name'
            ], $this->getValue());
    }
}
