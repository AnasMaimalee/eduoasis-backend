<?php

namespace App\Services\Payout;
use Illuminate\Support\Str;
use App\Enums\PayoutStatus;
use App\Models\PayoutRequest;
use App\Models\User;
use App\Services\WalletService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminPayoutService
{
    public function __construct(
        protected PaystackPayoutService $paystack,
        protected WalletService $walletService
    ) {}

    /* =====================================================
        ADMIN REQUEST PAYOUT
    ====================================================== */
    public function request(User $admin, float $amount): array
    {
        if (! $admin->hasRole('administrator')) {
            abort(403, 'Only administrators can request payouts.');
        }

        $bank = $admin->bankAccount;
        if (! $bank) {
            abort(422, 'Please go to setting and set bank details first.');
        }

        $wallet = $admin->wallet;
        if (! $wallet || $amount > $wallet->balance) {
            abort(422, 'Insufficient wallet balance.');
        }

        if (
            $admin->payoutRequests()
                ->where('status', PayoutStatus::PENDING)
                ->exists()
        ) {
            abort(422, 'You already have a pending payout request.');
        }

        if ($amount < 1000) {
            abort(422, 'Minimum payout amount is ₦1,000.');
        }

        // 🔥 Ensure Paystack recipient exists
        if (empty($bank->recipient_code)) {
            $this->createPaystackRecipient($admin);
            $bank->refresh();
        }

        if (empty($bank->recipient_code)) {
            abort(422, 'Unable to configure bank recipient. Please contact support.');
        }

        // ✅ CREATE PAYOUT REQUEST
        PayoutRequest::create([
            'admin_id'         => $admin->id,
            'amount'           => (int) ($amount), // store in kobo
            'status'           => PayoutStatus::PENDING,
            'balance_snapshot' => $wallet->balance,
        ]);

        return [
            'success' => true,
            'message' => 'Payout request submitted successfully',
            'amount'  => $amount,
        ];
    }

    /* =====================================================
        SUPERADMIN APPROVES & PAYS
    ====================================================== */

    public function approveAndPay(PayoutRequest $payout, User $superAdmin): array
    {
        if (! $superAdmin->hasRole('superadmin')) {
            abort(403, 'Only superadmin can approve payouts.');
        }

        if ($payout->status === 'paid') {
            abort(409, 'This payout has already been processed.');
        }

        $admin = $payout->administrator;

        return DB::transaction(function () use ($payout, $admin, $superAdmin) {

            // 🔹 Parent reference for this payout
            $parentReference = 'CASHOUT-' . Str::uuid();

            // 🔹 Debit ADMIN wallet (record history)
            $this->walletService->debitUser(
                $admin,
                $payout->amount,
                'Administrator payout approved',
                $parentReference . '-ADMIN'
            );

            // 🔹 Debit SUPERADMIN wallet (record history)
            $this->walletService->debitUser(
                $superAdmin,
                $payout->amount,
                'Administrator payout disbursement',
                $parentReference . '-SUPERADMIN'
            );

            // 🔹 Update payout record
            $payout->update([
                'status'      => 'paid',
                'approved_by' => $superAdmin->id,
                'reference'   => $parentReference,
                'paid_at'     => now(),
            ]);

            return [
                'success'   => true,
                'message'   => 'Payout approved successfully (manual payment)',
                'amount'    => $payout->amount,
                'reference' => $parentReference,
            ];
        });
    }




    /* =====================================================
        SUPERADMIN REJECTS PAYOUT
    ====================================================== */
    public function reject(
        PayoutRequest $payout,
        User $superAdmin,
        string $reason
    ): array {
        if (! $superAdmin->hasRole('superadmin')) {
            abort(403, 'Only superadmin can reject payouts.');
        }

        if (! trim($reason)) {
            abort(422, 'Rejection reason is required.');
        }

        $payout->update([
            'status'           => 'rejected',
            'rejection_reason' => $reason,
            'rejected_at'      => now(),
            'approved_by'      => $superAdmin->id,
        ]);

        return [
            'success' => true,
            'message' => 'Payout rejected successfully',
            'amount'  => $payout->amount,
        ];
    }


    /* =====================================================
        CREATE PAYSTACK RECIPIENT (PRIVATE)
    ====================================================== */
    private function createPaystackRecipient(User $admin): void
    {
        $bank = $admin->bankAccount;

        $response = $this->paystack->createRecipient([
            'type'           => 'nuban',
            'name'           => $bank->account_name,
            'account_number' => $bank->account_number,
            'bank_code'      => $bank->bank_code,
            'currency'       => 'NGN',
        ]);

        Log::info('Paystack recipient response', $response);

        if (! ($response['status'] ?? false)) {
            abort(422, 'Paystack error: ' . ($response['message'] ?? 'Recipient creation failed'));
        }

        $bank->update([
            'recipient_code' => $response['data']['recipient_code'],
        ]);
    }
}
