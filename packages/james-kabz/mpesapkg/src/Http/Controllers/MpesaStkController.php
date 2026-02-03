<?php

namespace JamesKabz\MpesaPkg\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use JamesKabz\MpesaPkg\MpesaClient;
use JamesKabz\MpesaPkg\Models\MpesaCallback;
use JamesKabz\MpesaPkg\Models\MpesaRequest;

class MpesaStkController
{
    public function push(Request $request, MpesaClient $client): JsonResponse
    {
        $data = $request->validate([
            'phone' => ['required', 'string'],
            'amount' => ['required', 'numeric', 'min:1'],
            'account_reference' => ['nullable', 'string', 'max:50'],
            'transaction_desc' => ['nullable', 'string', 'max:200'],
            'callback_url' => ['nullable', 'url'],
            'transaction_type' => ['nullable', 'string', 'max:50'],
            'party_b' => ['nullable', 'string', 'max:20'],
        ]);

        $result = $client->stkPush([
            'phone' => $data['phone'],
            'amount' => $data['amount'],
            'account_reference' => $data['account_reference'] ?? null,
            'transaction_desc' => $data['transaction_desc'] ?? null,
            'callback_url' => $data['callback_url'] ?? null,
            'transaction_type' => $data['transaction_type'] ?? null,
            'party_b' => $data['party_b'] ?? null,
        ]);

        if (config('mpesa.store_requests', true)) {
            try {
                MpesaRequest::create([
                    'type' => 'stk',
                    'phone' => $data['phone'],
                    'amount' => $data['amount'],
                    'response_code' => data_get($result, 'data.ResponseCode'),
                    'response_description' => data_get($result, 'data.ResponseDescription'),
                    'request_payload' => [
                        'phone' => $data['phone'],
                        'amount' => $data['amount'],
                        'account_reference' => $data['account_reference'] ?? null,
                        'transaction_desc' => $data['transaction_desc'] ?? null,
                        'callback_url' => $data['callback_url'] ?? null,
                        'transaction_type' => $data['transaction_type'] ?? null,
                        'party_b' => $data['party_b'] ?? null,
                    ],
                    'response_payload' => $result['data'] ?? $result,
                ]);
            } catch (\Throwable $e) {
                Log::warning('Failed to persist STK request', ['error' => $e->getMessage()]);
            }
        }

        return response()->json($result, $result['ok'] ? 200 : 400);
    }

    public function callback(Request $request): JsonResponse
    {
        Log::info('M-Pesa STK callback received', $request->all());
        $payload = $request->all();
        $stkCallback = data_get($payload, 'Body.stkCallback');

        if (! is_array($stkCallback) || data_get($stkCallback, 'ResultCode') === null) {
            Log::warning('M-Pesa STK callback missing required fields', [
                'payload' => $payload,
            ]);

            return response()->json([
                'ResultCode' => 0,
                'ResultDesc' => 'Accepted',
            ]);
        }

        $metadata = data_get($stkCallback, 'CallbackMetadata.Item', []);
        $receipt = collect($metadata)->firstWhere('Name', 'MpesaReceiptNumber');

        if (config('mpesa.store_callbacks', true)) {
            try {
                MpesaCallback::create([
                    'type' => 'stk',
                    'result_code' => data_get($stkCallback, 'ResultCode'),
                    'result_desc' => data_get($stkCallback, 'ResultDesc'),
                    'originator_conversation_id' => data_get($stkCallback, 'MerchantRequestID'),
                    'conversation_id' => data_get($stkCallback, 'CheckoutRequestID'),
                    'transaction_id' => is_array($receipt) ? ($receipt['Value'] ?? null) : null,
                    'payload' => $payload,
                ]);
            } catch (\Throwable $e) {
                Log::warning('Failed to persist STK callback', ['error' => $e->getMessage()]);
            }
        }

        return response()->json([
            'ResultCode' => 0,
            'ResultDesc' => 'Accepted',
        ]);
    }
}
