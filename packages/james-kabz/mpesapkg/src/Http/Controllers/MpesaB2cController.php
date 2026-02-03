<?php

namespace JamesKabz\MpesaPkg\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use JamesKabz\MpesaPkg\MpesaClient;
use JamesKabz\MpesaPkg\Models\MpesaCallback;
use JamesKabz\MpesaPkg\Models\MpesaRequest;

class MpesaB2cController
{
    public function send(Request $request, MpesaClient $client): JsonResponse
    {
        $data = $request->validate([
            'phone' => ['required', 'string'],
            'amount' => ['required', 'numeric', 'min:1'],
            'remarks' => ['nullable', 'string', 'max:200'],
            'occasion' => ['nullable', 'string', 'max:200'],
            'originator_conversation_id' => ['nullable', 'string', 'max:100'],
        ]);

        $result = $client->b2c([
            'phone' => $data['phone'],
            'amount' => $data['amount'],
            'remarks' => $data['remarks'] ?? null,
            'occasion' => $data['occasion'] ?? null,
            'originator_conversation_id' => $data['originator_conversation_id'] ?? null,
        ]);

        if (config('mpesa.store_requests', true)) {
            try {
                MpesaRequest::create([
                    'type' => 'b2c',
                    'phone' => $data['phone'],
                    'amount' => $data['amount'],
                    'remarks' => $data['remarks'] ?? null,
                    'originator_conversation_id' => data_get($result, 'data.OriginatorConversationID'),
                    'conversation_id' => data_get($result, 'data.ConversationID'),
                    'response_code' => data_get($result, 'data.ResponseCode'),
                    'response_description' => data_get($result, 'data.ResponseDescription'),
                    'request_payload' => [
                        'phone' => $data['phone'],
                        'amount' => $data['amount'],
                        'remarks' => $data['remarks'] ?? null,
                        'occasion' => $data['occasion'] ?? null,
                        'originator_conversation_id' => $data['originator_conversation_id'] ?? null,
                    ],
                    'response_payload' => $result['data'] ?? $result,
                ]);
            } catch (\Throwable $e) {
                Log::warning('Failed to persist B2C request', ['error' => $e->getMessage()]);
            }
        }

        return response()->json($result, $result['ok'] ? 200 : 400);
    }

    public function result(Request $request): JsonResponse
    {
        Log::info('M-Pesa B2C result received', $request->all());
        $payload = $request->all();

        if (config('mpesa.store_callbacks', true)) {
            try {
                MpesaCallback::create([
                    'type' => 'b2c_result',
                    'result_code' => data_get($payload, 'Result.ResultCode'),
                    'result_desc' => data_get($payload, 'Result.ResultDesc'),
                    'originator_conversation_id' => data_get($payload, 'Result.OriginatorConversationID'),
                    'conversation_id' => data_get($payload, 'Result.ConversationID'),
                    'transaction_id' => data_get($payload, 'Result.TransactionID'),
                    'payload' => $payload,
                ]);
            } catch (\Throwable $e) {
                Log::warning('Failed to persist B2C result callback', ['error' => $e->getMessage()]);
            }
        }

        return response()->json([
            'ResultCode' => 0,
            'ResultDesc' => 'Accepted',
        ]);
    }

    public function timeout(Request $request): JsonResponse
    {
        Log::info('M-Pesa B2C timeout received', $request->all());
        $payload = $request->all();

        if (config('mpesa.store_callbacks', true)) {
            try {
                MpesaCallback::create([
                    'type' => 'b2c_timeout',
                    'result_code' => data_get($payload, 'Result.ResultCode'),
                    'result_desc' => data_get($payload, 'Result.ResultDesc'),
                    'originator_conversation_id' => data_get($payload, 'Result.OriginatorConversationID'),
                    'conversation_id' => data_get($payload, 'Result.ConversationID'),
                    'transaction_id' => data_get($payload, 'Result.TransactionID'),
                    'payload' => $payload,
                ]);
            } catch (\Throwable $e) {
                Log::warning('Failed to persist B2C timeout callback', ['error' => $e->getMessage()]);
            }
        }

        return response()->json([
            'ResultCode' => 0,
            'ResultDesc' => 'Accepted',
        ]);
    }
}
