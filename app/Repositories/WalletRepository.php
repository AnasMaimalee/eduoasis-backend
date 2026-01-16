<?php

namespace App\Repositories;

use App\Models\Wallet;
use App\Models\WalletTransaction;

class WalletRepository
{
    public function getByUserId(string $userId): Wallet
    {
        return Wallet::where('user_id', $userId)
            ->lockForUpdate() // 🔒 CRITICAL
            ->firstOrFail();
    }

    public function createTransaction(array $data): WalletTransaction
    {
        return WalletTransaction::create($data);
    }

    public function updateBalance(Wallet $wallet, float $amount): Wallet
    {
        $wallet->update(['balance' => $amount]);
        return $wallet;
    }

    public function transactionExists(string $groupReference): bool
    {
        return WalletTransaction::where('group_reference', $groupReference)->exists();
    }

    /* 🚫 KEEP but deprecate (do NOT remove) */
    public function lockForUpdate(): self
    {
        return $this; // noop – legacy safety
    }
    // New method for locking the row
    public function getByUserIdForUpdate(string $userId)
    {
        return Wallet::where('user_id', $userId)->lockForUpdate()->firstOrFail();
    }

    public function transactionsQuery()
    {
        return WalletTransaction::query();
    }

    public function createPayoutTransaction(array $data): WalletTransaction
    {
        return WalletTransaction::create($data);
    }
}
