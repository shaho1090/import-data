<?php

namespace App\Services\Interfaces;

use App\Filters\QueryFilter;

interface LogServiceInterface
{
    public function insertMany(array $lines): bool;
    public function filterAndCount(QueryFilter $filters);
}
