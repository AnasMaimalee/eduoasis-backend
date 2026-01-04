<?php

namespace App\Http\Controllers\Api\Wallet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\WalletService;

class WalletController extends Controller
{
    public function __construct(
        protected WalletService $walletService
    ) {}

    public function index()
    {
        $wallet = auth()->user()
            ->wallet()
            ->with('transactions')
            ->first();

        return response()->json([
            'balance' => $wallet->balance,
            'transactions' => $wallet->transactions
        ]);
    }

    // GET MY WALLET
    public function me(Request $request)
    {
        return response()->json(
            $this->walletService->getWallet(auth()->user())
        );
    }

    // WALLET TRANSACTIONS
    public function transactions()
    {
        return response()->json(
            $this->walletService->transactions(auth()->user())
        );
    }
}
