<?php


namespace App\Models\Traits;

use App\Http\Filters\AbstractFilter;
use Illuminate\Database\Eloquent\Builder;

trait Filterable
{
    public function scopeFilter(Builder $builder, AbstractFilter $filter)
    {
        $filter->apply($builder);

        return $builder;
    }
}