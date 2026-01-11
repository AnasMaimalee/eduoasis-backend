<?php

namespace App\Http\Controllers\Api\Webhooks;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Paystack\PaystackWebhookService;
use Illuminate\Support\Facades\Log;

class PaystackWebhookController extends Controller
{
    public function handle(Request $request, PaystackWebhookService $service)
    {
        $rawBody = $request->getContent();

        // LOUD HIT LOG - keep this forever
        Log::emergency('!!! WEBHOOK CONTROLLER WAS HIT !!!', [
            'time' => now()->toDateTimeString(),
            'ip' => $request->ip(),
            'url' => $request->fullUrl(),
            'signature' => $request->header('x-paystack-signature') ?? 'missing',
            'body_preview' => substr($rawBody, 0, 300) . '...',
        ]);

        if (!$request->isMethod('post')) {
            return response()->json(['status' => 'wrong_method'], 405);
        }

        // TEMPORARILY DISABLE SIGNATURE CHECK FOR DEBUGGING
        // REMOVE THIS IN PRODUCTION AFTER CONFIRMING IT WORKS!
        Log::warning('SIGNATURE VERIFICATION SKIPPED FOR DEBUGGING - ENABLE BEFORE LIVE!');
        // $signature = $request->header('x-paystack-signature');
        // $expected = hash_hmac('sha512', $rawBody, config('services.paystack.secret'));
        // if (!$signature || $signature !== $expected) {
        //     Log::error('Signature mismatch', [...]);
        //     abort(401);
        // }

        $data = json_decode($rawBody, true);

        if (!$data || !isset($data['event'])) {
            Log::warning('Invalid payload', ['payload' => $rawBody]);
            return response()->json(['status' => 'invalid_payload'], 400);
        }

        try {
            Log::info('Calling webhook service', ['event' => $data['event']]);
            $service->handle($data);
            Log::info('Webhook processed successfully', ['event' => $data['event']]);
        } catch (\Exception $e) {
            Log::critical('Webhook processing CRASHED', [
                'event' => $data['event'] ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['status' => 'error'], 500);
        }

        return response()->json(['status' => 'ok']);
    }
}
