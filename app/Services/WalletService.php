<?php

namespace App\Services;

use App\Models\User;
use App\Models\WalletTransaction;
use App\Repositories\WalletRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\WalletCredited;
use App\Mail\WalletDebited;

class WalletService
{
    public function __construct(
        protected WalletRepository $walletRepo
    ) {}

    /* ===============================
        HELPERS
    ================================ */

    protected function getSuperAdmin(): User
    {
        $admin = User::role('superadmin')->first();

        if (! $admin) {
            abort(500, 'Super admin not found');
        }

        return $admin;
    }

    protected function ensureSuperAdmin(User $user): void
    {
        if (! $user->hasRole('superadmin')) {
            abort(403, 'Only super admin can perform this action');
        }
    }

    protected function reference(): string
    {
        return (string) Str::uuid();
    }

    /* ===============================
        LOW-LEVEL LEDGER (PROTECTED)
    ================================ */

    protected function credit(
        User $user,
        float $amount,
        string $description,
        string $groupReference
    ): WalletTransaction {
        $wallet = $this->walletRepo->getByUserId($user->id);

        $before = $wallet->balance;
        $after  = $before + $amount;

        $this->walletRepo->updateBalance($wallet, $after);

        return $this->walletRepo->createTransaction([
            'user_id'         => $user->id,
            'wallet_id'       => $wallet->id,
            'type'            => 'credit',
            'amount'          => $amount,
            'balance_before'  => $before,
            'balance_after'   => $after,
            'reference'       => (string) Str::uuid(),
            'group_reference' => $groupReference,
            'description'     => $description,
        ]);
    }

    protected function debit(
        User $user,
        float $amount,
        string $description,
        string $groupReference
    ): WalletTransaction {
        $wallet = $this->walletRepo->getByUserId($user->id);

        if ($wallet->balance < $amount) {
            abort(422, 'Insufficient wallet balance');
        }

        $before = $wallet->balance;
        $after  = $before - $amount;

        $this->walletRepo->updateBalance($wallet, $after);

        return $this->walletRepo->createTransaction([
            'user_id'         => $user->id,
            'wallet_id'       => $wallet->id,
            'type'            => 'debit',
            'amount'          => $amount,
            'balance_before'  => $before,
            'balance_after'   => $after,
            'reference'       => (string) Str::uuid(),
            'group_reference' => $groupReference,
            'description'     => $description,
        ]);
    }

    /* ===============================
        PUBLIC METHODS (SAFE TO USE FROM OTHER SERVICES)
    ================================ */

    /**
     * Credit a user's wallet
     */
    public function creditUser(
        User $user,
        float $amount,
        string $description,
        string $groupReference = null
    ): WalletTransaction {
        return $this->credit(
            $user,
            $amount,
            $description,
            $groupReference ?? $this->reference()
        );
    }

    /**
     * Debit a user's wallet
     */
    public function debitUser(
        User $user,
        float $amount,
        string $description,
        string $groupReference = null
    ): WalletTransaction {
        return $this->debit(
            $user,
            $amount,
            $description,
            $groupReference ?? $this->reference()
        );
    }

    /**
     * Transfer money from one user to another (e.g., refunds, payouts)
     */
    public function transfer(
        User $from,
        User $to,
        float $amount,
        string $description,
        string $groupReference = null
    ): array {
        $groupRef = $groupReference ?? $this->reference();

        return DB::transaction(function () use ($from, $to, $amount, $description, $groupRef) {
            $debitTx = $this->debit($from, $amount, $description, $groupRef);
            $creditTx = $this->credit($to, $amount, $description, $groupRef);

            return [
                'debit_transaction'  => $debitTx,
                'credit_transaction' => $creditTx,
            ];
        });
    }

    /* ===============================
        ADMIN OPERATIONS (WITH EMAILS)
    ================================ */

    public function adminCreditUser(
        User $admin,
        User $user,
        float $amount,
        string $reason
    ): void {
        $this->ensureSuperAdmin($admin);

        $superAdmin = $this->getSuperAdmin();
        $ref = $this->reference();

        $creditTx = null;

        DB::transaction(function () use ($superAdmin, $user, $amount, $reason, $ref, &$creditTx) {
            $this->debitUser($superAdmin, $amount, "Admin funding user ({$user->email}): {$reason}", $ref);
            $creditTx = $this->creditUser($user, $amount, "Wallet funded by admin: {$reason}", $ref);
        });

        if ($creditTx) {
            Mail::to($user->email)->send(
                new WalletCredited(
                    $user,
                    $amount,
                    $creditTx->balance_after,
                    $reason
                )
            );
        }
    }

    public function adminDebitUser(
        User $admin,
        User $user,
        float $amount,
        string $reason
    ): void {
        $this->ensureSuperAdmin($admin);

        $superAdmin = $this->getSuperAdmin();
        $ref = $this->reference();

        $debitTx = null;

        DB::transaction(function () use ($superAdmin, $user, $amount, $reason, $ref, &$debitTx) {
            $debitTx = $this->debitUser($user, $amount, "Admin debit: {$reason}", $ref);
            $this->creditUser($superAdmin, $amount, "Collected from user ({$user->email}): {$reason}", $ref);
        });

        Mail::to($user->email)->send(
            new WalletDebited(
                $user,
                $amount,
                $debitTx->balance_after,
                $reason
            )
        );
    }

    /* ===============================
        READ OPERATIONS
    ================================ */

    public function transactions(User $user, int $perPage = 20): array
    {
        $wallet = $this->walletRepo->getByUserId($user->id);

        return [
            'current_balance' => $wallet->balance,
            'transactions'    => $wallet->transactions()
                ->latest()
                ->paginate($perPage)
        ];
    }

    /**
     * Credit user wallet without sending email (for seeding, testing, migrations)
     */
    public function creditUserQuietly(
        User $user,
        float $amount,
        string $description,
        string $groupReference = null
    ): WalletTransaction {
        return $this->credit(
            $user,
            $amount,
            $description,
            $groupReference ?? $this->reference()
        );
    }

    /**
     * Get wallet details for logged-in user
     */
    public function getWallet(User $user): array
    {
        $wallet = $this->walletRepo->getByUserId($user->id);

        return [
            'id'       => $wallet->id,
            'balance'  => $wallet->balance,
            'currency' => $wallet->currency ?? 'NGN',
            'created_at' => $wallet->created_at,
        ];
    }

}
