<?php

namespace App\Http\Controllers\Logs;

use App\Filters\LogQueryFilter;
use App\Http\Controllers\Controller;
use App\Models\Log;
use Illuminate\Http\JsonResponse;

class LogCountController extends Controller
{
    public function index(LogQueryFilter $filters): JsonResponse
    {
        $count = Log::query()->filter($filters)->count();

        return response()->json([
            'count' => $count
        ]);
    }
}
