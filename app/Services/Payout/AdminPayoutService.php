<?php

namespace App\Services\Payout;

use App\Enums\PayoutStatus;
use App\Models\PayoutRequest;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminPayoutService
{
    public function __construct(
        protected PaystackPayoutService $paystack
    ) {}

    /**
     * Admin requests a payout from their wallet
     */
    public function request(User $admin, float $amount): PayoutRequest
    {
        // Role check
        if (! $admin->hasRole('administrator')) {
            abort(403, 'Only administrators can request payouts.');
        }

        // Bank details check
        if (! $admin->bankAccount) {
            abort(422, 'Please set bank details first');
        }

        // Ensure recipient code exists in Paystack (create if not)
        if (empty($admin->bankAccount->recipient_code)) {
            $this->createPaystackRecipient($admin);
        }

        // Wallet balance check
        $wallet = $admin->wallet;
        if (! $wallet || $amount > $wallet->balance) {
            abort(422, 'Insufficient wallet balance');
        }

        // Prevent multiple pending requests
        if ($admin->payoutRequests()->where('status', PayoutStatus::PENDING->value)->exists()) {
            abort(422, 'You already have a pending payout request');
        }

        // Minimum amount (optional - adjust as needed)
        if ($amount < 1000) { // e.g., ₦1000 minimum
            abort(422, 'Minimum payout amount is ₦1,000');
        }

        return PayoutRequest::create([
            'admin_id'         => $admin->id,
            'amount'           => $amount * 100, // Paystack uses kobo (amount in cents)
            'status'           => PayoutStatus::PENDING,
            'balance_snapshot' => $wallet->balance,
        ]);
    }

    /**
     * Superadmin approves payout and triggers Paystack transfer
     */
    public function approveAndPay(PayoutRequest $payout, User $superAdmin): array
    {
        // Authorization
        if (! $superAdmin->hasRole('superadmin')) {
            abort(403, 'Only superadmin can approve payouts.');
        }

        if ($payout->status !== PayoutStatus::PENDING) {
            abort(422, 'Payout is not in pending state.');
        }

        $admin = $payout->admin;
        $bankAccount = $admin->bankAccount;

        if (! $bankAccount || empty($bankAccount->recipient_code)) {
            abort(422, 'Admin bank recipient not configured.');
        }

        return DB::transaction(function () use ($payout, $admin, $bankAccount, $superAdmin) {
            try {
                // Initiate transfer via Paystack
                $transferResponse = $this->paystack->initiateTransfer([
                    'source'    => 'balance',
                    'amount'    => $payout->amount, // already in kobo
                    'recipient' => $bankAccount->recipient_code,
                    'reason'    => 'Admin earnings payout',
                ]);

                if ($transferResponse['status'] !== true) {
                    throw new Exception('Paystack transfer failed: ' . ($transferResponse['message'] ?? 'Unknown error'));
                }

                $reference = $transferResponse['data']['reference'] ?? null;

                // Debit wallet only after successful initiation
                app('wallet')->debit(
                    $admin,
                    $payout->amount / 100, // convert back to naira for wallet
                    'Payout withdrawal',
                    ['payout_id' => $payout->id, 'reference' => $reference]
                );

                // Update payout record
                $payout->update([
                    'status'      => PayoutStatus::PAID,
                    'approved_by' => $superAdmin->id,
                    'approved_at' => now(),
                    'reference'   => $reference,
                    'paid_at'     => now(),
                ]);

                return [
                    'message'   => 'Payout processed successfully',
                    'payout_id' => $payout->id,
                    'reference' => $reference,
                    'amount'    => $payout->amount / 100,
                    'status'    => 'completed',
                ];

            } catch (Exception $e) {
                Log::error('Payout approval failed', [
                    'payout_id' => $payout->id,
                    'error'     => $e->getMessage(),
                    'trace'     => $e->getTraceAsString(),
                ]);

                abort(500, 'Failed to process payout. Please try again later.');
            }
        });
    }

    /**
     * Create Paystack transfer recipient if not exists
     */
    private function createPaystackRecipient(User $admin): void
    {
        $bank = $admin->bankAccount;

        try {
            $response = $this->paystack->createRecipient([
                'type'          => 'nuban',
                'name'          => $bank->account_name,
                'account_number'=> $bank->account_number,
                'bank_code'     => $bank->bank_code,
                'currency'      => 'NGN',
            ]);

            if ($response['status'] === true) {
                $recipientCode = $response['data']['recipient_code'];

                $bank->update([
                    'recipient_code' => $recipientCode,
                ]);
            } else {
                throw new Exception('Failed to create Paystack recipient');
            }
        } catch (Exception $e) {
            Log::error('Failed to create Paystack recipient for admin', [
                'admin_id' => $admin->id,
                'error'    => $e->getMessage(),
            ]);

            abort(422, 'Unable to link bank account with payment provider. Contact support.');
        }
    }
}
