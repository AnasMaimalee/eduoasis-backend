<?php

namespace App\Http\Controllers\Api\Payout;

use App\Http\Controllers\Controller;
use App\Models\PayoutRequest;
use App\Services\Payout\AdminPayoutService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminPayoutController extends Controller
{
    public function listRequests(Request $request): JsonResponse
    {
        $user = auth()->user();

        $query = PayoutRequest::with('admin') // optional: load admin name/email
        ->latest();

        if (! $user->hasRole('administrator')) {
            $query->where('admin_id', $user->id);
        }

        $payouts = $query->paginate(20);

        return response()->json([
            'message' => 'Payout requests retrieved successfully',
            'data' => $payouts,
        ]);
    }
    /**
     * Admin requests payout
     */
    public function requestPayout(
        Request $request,
        AdminPayoutService $service
    ): JsonResponse {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
        ]);

        $payout = $service->request(
            auth()->user(),
            $validated['amount']
        );

        return response()->json([
            'message' => 'Payout request submitted successfully',
            'data'    => $payout,
        ], 201);
    }

    /**
     * Super Admin approves & triggers Paystack payout
     */
    public function approvePayout(
        PayoutRequest $payout,
        AdminPayoutService $service
    ): JsonResponse {
        $result = $service->approveAndPay(
            $payout,
            auth()->user()
        );

        return response()->json([
            'message' => 'Payout approved and processed successfully',
            'data'    => $result,
        ]);
    }
}
