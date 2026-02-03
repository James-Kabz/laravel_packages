<?php

namespace JamesKabz\MpesaPkg\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use JamesKabz\MpesaPkg\Models\MpesaCallback;

class MpesaC2bController
{
    public function validation(Request $request): JsonResponse
    {
        Log::info('M-Pesa C2B validation received', $request->all());
        $payload = $request->all();

        if (config('mpesa.store_callbacks', true)) {
            try {
                MpesaCallback::create([
                    'type' => 'c2b_validation',
                    'result_code' => data_get($payload, 'ResultCode'),
                    'result_desc' => data_get($payload, 'ResultDesc'),
                    'transaction_id' => data_get($payload, 'TransID'),
                    'mpesa_receipt_number' => data_get($payload, 'TransID'),
                    'bill_ref_number' => data_get($payload, 'BillRefNumber'),
                    'amount' => data_get($payload, 'TransAmount'),
                    'phone' => data_get($payload, 'MSISDN'),
                    'party_a' => data_get($payload, 'MSISDN'),
                    'party_b' => data_get($payload, 'BusinessShortCode'),
                    'payload' => $payload,
                ]);
            } catch (\Throwable $e) {
                Log::warning('Failed to persist C2B validation callback', ['error' => $e->getMessage()]);
            }
        }

        return response()->json([
            'ResultCode' => 0,
            'ResultDesc' => 'Accepted',
        ]);
    }

    public function confirmation(Request $request): JsonResponse
    {
        Log::info('M-Pesa C2B confirmation received', $request->all());
        $payload = $request->all();

        if (config('mpesa.store_callbacks', true)) {
            try {
                MpesaCallback::create([
                    'type' => 'c2b_confirmation',
                    'result_code' => data_get($payload, 'ResultCode'),
                    'result_desc' => data_get($payload, 'ResultDesc'),
                    'transaction_id' => data_get($payload, 'TransID'),
                    'mpesa_receipt_number' => data_get($payload, 'TransID'),
                    'bill_ref_number' => data_get($payload, 'BillRefNumber'),
                    'amount' => data_get($payload, 'TransAmount'),
                    'phone' => data_get($payload, 'MSISDN'),
                    'party_a' => data_get($payload, 'MSISDN'),
                    'party_b' => data_get($payload, 'BusinessShortCode'),
                    'payload' => $payload,
                ]);
            } catch (\Throwable $e) {
                Log::warning('Failed to persist C2B confirmation callback', ['error' => $e->getMessage()]);
            }
        }

        return response()->json([
            'ResultCode' => 0,
            'ResultDesc' => 'Accepted',
        ]);
    }
}
