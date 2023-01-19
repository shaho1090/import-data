<?php

namespace App\Filters;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

abstract class QueryFilter
{
    protected Request $request;

    protected Builder $builder;

    protected Collection $builder_parameters;

    /**
     * QueryFilter constructor.
     * @param Request $request
     * @param array $builder_parameters
     */
    public function __construct(Request $request, array $builder_parameters = [])
    {
        $this->request = $request;
        $this->builder_parameters = collect($builder_parameters);
    }

    /**
     * @param Builder $builder
     * @return Builder
     */
    public function apply(Builder $builder): Builder
    {
        $this->builder = $builder;

        foreach ($this->filters() as $name => $value) {
            if (method_exists($this, $name)) {
                call_user_func_array([$this, $name], array_filter([$value]));
            }
        }

        return $this->builder;
    }

    /**
     * @return array
     */
    public function filters(): array
    {
        return $this->request->toArray();
    }

    /**
     * @return Request
     */
    public function request(): Request
    {
        return $this->request;
    }
}
