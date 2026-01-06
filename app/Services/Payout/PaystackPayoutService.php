<?php

namespace App\Services\Payout;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaystackPayoutService
{
    protected string $baseUrl = 'https://api.paystack.co';
    protected string $secretKey;

    public function __construct()
    {
        $this->secretKey = config('services.paystack.secret_key');
        if (! $this->secretKey) {
            throw new Exception('Paystack secret key not configured.');
        }
    }

    /**
     * Create a transfer recipient on Paystack
     */
    public function createRecipient(array $data): array
    {
        $payload = [
            'type'               => $data['type'], // 'nuban'
            'name'               => $data['name'],
            'account_number'     => $data['account_number'],
            'bank_code'          => $data['bank_code'],
            'currency'           => $data['currency'] ?? 'NGN',
        ];

        $response = Http::withToken($this->secretKey)
            ->post("{$this->baseUrl}/transferrecipient", $payload);

        $body = $response->json();

        if (! $response->successful() || $body['status'] !== true) {
            Log::error('Paystack recipient creation failed', [
                'payload' => $payload,
                'response' => $body,
            ]);

            throw new Exception(
                $body['message'] ?? 'Failed to create Paystack transfer recipient'
            );
        }

        return $body; // Contains 'data.recipient_code'
    }

    /**
     * Initiate a transfer (payout) using recipient code
     */
    public function initiateTransfer(array $data): array
    {
        $payload = [
            'source'    => $data['source'] ?? 'balance',
            'amount'    => $data['amount'], // must be in kobo already
            'recipient' => $data['recipient'],
            'reason'    => $data['reason'] ?? 'Admin payout',
        ];

        $response = Http::withToken($this->secretKey)
            ->post("{$this->baseUrl}/transfer", $payload);

        $body = $response->json();

        if (! $response->successful() || $body['status'] !== true) {
            Log::error('Paystack transfer initiation failed', [
                'payload' => $payload,
                'response' => $body,
            ]);

            throw new Exception(
                $body['message'] ?? 'Paystack transfer failed'
            );
        }

        return $body; // Contains 'data.reference', etc.
    }
}
