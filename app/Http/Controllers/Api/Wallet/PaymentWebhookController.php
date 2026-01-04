<?php

namespace App\Http\Controllers\Api\Wallet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Services\WalletService;
use App\Models\User;

class PaymentWebhookController extends Controller
{
    public function handle(Request $request, WalletService $walletService)
    {
        $signature = $request->header('x-paystack-signature');
        $secret = config('services.paystack.secret');

        if (!$signature || $signature !== hash_hmac('sha512', $request->getContent(), $secret)) {
            return response()->json(['message' => 'Invalid signature'], 401);
        }

        $payload = $request->all();

        if ($payload['event'] !== 'charge.success') {
            return response()->json(['message' => 'Ignored'], 200);
        }

        $data = $payload['data'];
        $reference = $data['reference'];
        $amount = $data['amount'] / 100;
        $email = $data['customer']['email'];

        DB::transaction(function () use ($email, $amount, $reference, $walletService) {
            $user = User::where('email', $email)->first();

            if (!$user) return;

            $walletService->credit(
                $user,
                $amount,
                $reference,
                'Wallet funding via Paystack'
            );
        });

        return response()->json(['message' => 'Wallet credited']);
    }
}
