<?php

namespace App\Services\Concretes;

use App\Models\Log;
use App\Services\Interfaces\LogServiceInterface;
use Illuminate\Support\Facades\DB;

class LogService implements LogServiceInterface
{
    private string $databaseName = 'logs';

    public function insertMany($lines): bool
    {
        return DB::table($this->databaseName)->insert($lines);
    }

    public function filterAndCount($filters)
    {
        return Log::query()->filter($filters)->count();
    }
}
