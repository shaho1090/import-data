<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class LogService
{
    private string $databaseName = 'logs';

    public function insertMany($lines): bool
    {
        return DB::table($this->databaseName)->insert($lines);
    }
}
