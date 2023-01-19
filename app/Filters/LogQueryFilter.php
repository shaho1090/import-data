<?php

namespace App\Filters;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class LogQueryFilter extends QueryFilter
{
    /**
     * @param string $serviceName
     * @return Builder
     */
    public function serviceName(string $serviceName = ''): Builder
    {
        if(empty($serviceName)){
            return $this->builder;
        }

        return $this->builder->where('service_name',$serviceName);
    }

    /**
     * @param int|null $statusCode
     * @return Builder
     */
    public function statusCode(?int $statusCode = null): Builder
    {
        if(is_null($statusCode)){
            return $this->builder;
        }

        return $this->builder->where('status_code',$statusCode);
    }

    /**
     * @param $date
     * @return Builder
     */
    public function startDate($date = null): Builder
    {
        if (is_null($date)) {
            return $this->builder;
        }

        return $this->builder->whereDate('date', '>=', $date);
    }

    /**
     * @param $date
     * @return Builder
     */
    public function endDate($date = null): Builder
    {
        if (is_null($date)) {
            return $this->builder;
        }

        return $this->builder->whereDate('date', '<=', $date);
    }
}
