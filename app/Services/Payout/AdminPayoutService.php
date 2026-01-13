<?php

namespace App\Services\Payout;

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

        $admin = $payout->administrator;
        $bank  = $admin->bankAccount;

        if (! $bank) {
            abort(400, 'Admin bank details not found.');
        }

        return DB::transaction(function () use ($payout, $admin, $bank) {

            // 🔹 CREATE RECIPIENT IF NOT EXISTS
            if (! $bank->recipient_code) {
                $recipientResponse = $this->paystack->createRecipient([
                    'type'           => 'nuban',
                    'name'           => $bank->account_name,
                    'account_number' => $bank->account_number,
                    'bank_code'      => $bank->bank_code,
                    'currency'       => 'NGN',
                ]);

                $recipientCode = $recipientResponse['data']['recipient_code'] ?? null;

                if (! $recipientCode) {
                    abort(500, 'Unable to configure bank recipient. Please contact support.');
                }

                $bank->update([
                    'recipient_code' => $recipientCode,
                ]);
            }

            // 🔹 INITIATE PAYSTACK TRANSFER
            $transferResponse = $this->paystack->initiateTransfer([
                'amount'    => (int) ($payout->amount * 100), // convert to kobo
                'recipient' => $bank->recipient_code,
                'reason'    => "Admin payout for request #{$payout->id}",
            ]);

            // 🔹 UPDATE PAYOUT STATUS
            $payout->update([
                'status'      => PayoutStatus::PROCESSING,
                'approved_by' => auth()->id(),
                'reference'   => $transferResponse['data']['reference'] ?? null,
            ]);

            return [
                'success' => true,
                'message' => 'Payout initiated successfully',
                'amount'  => $payout->amount,
            ];
        });
    }

    /* =====================================================
        SUPERADMIN REJECTS PAYOUT
    ====================================================== */
    public function reject(PayoutRequest $payout, User $superAdmin): array
    {
        if (! $superAdmin->hasRole('superadmin')) {
            abort(403, 'Only superadmin can reject payouts.');
        }

        $payout->update([
            'status'      => PayoutStatus::REJECTED,
            'approved_by' => null,
            'approved_at' => null,
            'updated_at'  => now(),
        ]);

        return [
            'success'   => true,
            'message'   => 'Payout rejected successfully',
            'amount'    => $payout->amount,
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
