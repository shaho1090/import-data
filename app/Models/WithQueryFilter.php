<?php


namespace App\Models;


use App\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Builder;

Trait WithQueryFilter
{
    /**
     */
    public function scopeFilter($query, QueryFilter $filters): Builder
    {
        return $filters->apply($query);
    }
}
