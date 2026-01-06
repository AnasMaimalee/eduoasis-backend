<?php

namespace App\Http\Controllers\Api\Payout;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BankAccountController extends Controller
{
    /**
     * Get current user's bank details (if any)
     */
    public function show(Request $request): JsonResponse
    {
        $bankAccount = $request->user()->bankAccount;

        return response()->json([
            'data' => $bankAccount,
        ]);
    }

    /**
     * Create or Update user's bank account details
     */
    public function storeOrUpdate(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'bank_name'       => 'required|string|max:255',
            'account_name'    => 'required|string|max:255',
            'account_number'  => 'required|string|max:34',
            'bank_code'       => 'required|string|max:10',
        ]);

        $bankAccount = BankAccount::updateOrCreate(
            ['user_id' => $user->id],
            $validated
        );

        return response()->json([
            'message' => 'Bank details saved successfully',
            'data'    => $bankAccount->makeHidden(['user_id']),
        ], 200);
    }
}
