<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\Dashboard\UserDashboardService;
use Illuminate\Http\JsonResponse;

class UserDashboardController extends Controller
{
    public function index(UserDashboardService $service): JsonResponse
    {
        return response()->json(
            $service->summary(auth()->user())
        );
    }
}
