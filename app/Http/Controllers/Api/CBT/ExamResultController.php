<?php

namespace App\Http\Controllers\Api\CBT;
use App\Repositories\CBT\ExamRepository;
use App\Services\CBT\ExamResultService;
use App\Services\CBT\ExamService;
use App\Services\CBT\WalletPaymentService;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

class ExamResultController extends Controller
{
    public function __construct(
        protected ExamResultService $service,

    ) {}
    public function index(Request $request)
    {
        return response()->json([
            'data' => $this->service->history($request->user()->id)
        ]);
    }

}
