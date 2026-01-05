<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\Dashboard\SuperAdminDashboardService;
use Illuminate\Http\JsonResponse;

class SuperAdminDashboardController extends Controller
{
    public function index(SuperAdminDashboardService $service): JsonResponse
    {
        return response()->json(
            $service->summary()
        );
    }
}
