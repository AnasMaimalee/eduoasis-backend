<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\Dashboard\AdminDashboardService;
use Illuminate\Http\JsonResponse;

class AdminDashboardController extends Controller
{
    public function index(AdminDashboardService $service): JsonResponse
    {
        return response()->json(
            $service->summary(auth()->user())
        );
    }
}
