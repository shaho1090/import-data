<?php

namespace App\Http\Controllers\Logs;

use App\Filters\LogQueryFilter;
use App\Http\Controllers\Controller;
use App\Models\Log;
use App\Services\Interfaces\LogServiceInterface;
use Illuminate\Http\JsonResponse;

class LogCountController extends Controller
{
    public function __construct(
        private LogServiceInterface $logService)
    {
    }

    public function index(LogQueryFilter $filters): JsonResponse
    {
        return response()->json([
            'count' => $this->logService->filterAndCount($filters)
        ]);
    }
}
