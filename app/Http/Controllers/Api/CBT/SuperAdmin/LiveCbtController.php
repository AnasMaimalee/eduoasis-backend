<?php

namespace App\Http\Controllers\Api\CBT\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Services\CBT\SuperAdmin\LiveCbtService;

class LiveCbtController extends Controller
{
    public function __construct(
        protected LiveCbtService $service
    ) {}

    public function live()
    {
        return response()->json([
            'message' => 'Live CBT sessions fetched',
            'data' => $this->service->getLiveSessions()
        ]);
    }
}
