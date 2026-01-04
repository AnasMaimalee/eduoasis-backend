<?php

namespace App\Services;

use App\Models\User;
use App\Models\Service;
use App\Repositories\WalletRepository;
use App\Repositories\ServiceRequestRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\WalletDebited;

class ServiceRequestService
{
    public function __construct(
        protected WalletRepository $walletRepo,
        protected ServiceRequestRepository $requestRepo
    ) {}

    public function requestService(User $user, Service $service)
    {
        $wallet = $user->wallet;
        if ($wallet->balance < $service->price) {
            abort(422, 'Insufficient wallet balance');
        }

        return DB::transaction(function () use ($user, $service, $wallet) {
// Debit wallet
            $before = $wallet->balance;
            $after = $before - $service->price;
            $this->walletRepo->updateBalance($wallet, $after);

// Log wallet transaction
            $this->walletRepo->createTransaction([
                'wallet_id' => $wallet->id,
                'type' => 'debit',
                'amount' => $service->price,
                'balance_before' => $before,
                'balance_after' => $after,
                'reference' => \Str::uuid(),
                'description' => "Payment for service: {$service->name}",
            ]);

// Record service request
            $serviceRequest = $this->requestRepo->create([
                'user_id' => $user->id,
                'service_id' => $service->id,
                'amount' => $service->price,
                'status' => 'completed',
            ]);

// Send email
            Mail::to($user->email)->send(
                new WalletDebited($user, $service->price, $after)
            );

            return $serviceRequest;
        });
    }
}
