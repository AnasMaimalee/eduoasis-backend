<?php

namespace App\Repositories;

use App\Models\Wallet;
use App\Models\WalletTransaction;

class WalletRepository
{

    protected $query;
    public function transactionsQuery()
    {
        return WalletTransaction::query();
    }
    public function getByUserId(string $userId): Wallet
    {
        return Wallet::where('user_id', $userId)->firstOrFail();
    }


    public function lockForUpdate(): self
    {
        $this->query->lockForUpdate();
        return $this;
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

    public function createPayoutTransaction(array $data): WalletTransaction
    {
        return WalletTransaction::create($data);
    }

    public function transactionExists(string $groupReference): bool
    {
        return WalletTransaction::where('group_reference', $groupReference)->exists();
    }

}
