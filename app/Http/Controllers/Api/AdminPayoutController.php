<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AdminPayoutService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AdminPayoutController extends Controller
{
    public function __construct(
        protected AdminPayoutService $payoutService
    ) {
        $this->middleware(['auth:api', 'role:administrator']);
    }

    public function payout(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:100',
        ]);

        $amount = (float) $validated['amount'];

        // 🔒 Perform payout (no return exposed)
        $this->payoutService->payout(
            auth()->user(),
            $amount
        );

        return response()->json([
            'success' => true,
            'message' => 'Payout initiated successfully',
            'amount'  => $amount,
        ], 200);
    }
}
