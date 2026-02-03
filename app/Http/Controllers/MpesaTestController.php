<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\MpesaCallback;
use App\Models\MpesaRequest;
use JamesKabz\MpesaPkg\MpesaClient;

class MpesaTestController extends Controller
{
    public function index()
    {
        $latestCallback = MpesaCallback::latest()->first();

        return view('mpesa-test', [
            'latestCallback' => $latestCallback,
        ]);
    }

    public function token(MpesaClient $client)
    {
        $result = $client->getAccessToken();

        return back()->with('mpesa_result', $result);
    }

    public function stkPush(Request $request, MpesaClient $client)
    {
        $data = $request->validate([
            'phone' => ['required', 'string'],
            'amount' => ['required', 'numeric', 'min:1'],
        ]);

        $result = $client->stkPush([
            'phone' => $data['phone'],
            'amount' => $data['amount'],
        ]);

        MpesaRequest::create([
            'type' => 'stk',
            'phone' => $data['phone'],
            'amount' => $data['amount'],
            'response_code' => data_get($result, 'data.ResponseCode'),
            'response_description' => data_get($result, 'data.ResponseDescription'),
            'request_payload' => [
                'phone' => $data['phone'],
                'amount' => $data['amount'],
            ],
            'response_payload' => $result['data'] ?? $result,
        ]);

        return response()->json($result, $result['ok'] ? 200 : 400);
    }

    public function stkCallback(Request $request)
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

        MpesaCallback::create([
            'type' => 'stk',
            'result_code' => data_get($stkCallback, 'ResultCode'),
            'result_desc' => data_get($stkCallback, 'ResultDesc'),
            'originator_conversation_id' => data_get($stkCallback, 'MerchantRequestID'),
            'conversation_id' => data_get($stkCallback, 'CheckoutRequestID'),
            'transaction_id' => is_array($receipt) ? ($receipt['Value'] ?? null) : null,
            'payload' => $payload,
        ]);

        return response()->json([
            'ResultCode' => 0,
            'ResultDesc' => 'Accepted',
        ]);
    }

    public function b2c(Request $request, MpesaClient $client)
    {
        $data = $request->validate([
            'phone' => ['required', 'string'],
            'amount' => ['required', 'numeric', 'min:1'],
            'remarks' => ['nullable', 'string', 'max:200'],
        ]);

        $result = $client->b2c([
            'phone' => $data['phone'],
            'amount' => $data['amount'],
            'remarks' => $data['remarks'] ?? null,
        ]);

        MpesaRequest::create([
            'type' => 'b2c',
            'phone' => $data['phone'],
            'amount' => $data['amount'],
            'remarks' => $data['remarks'] ?? null,
            'originator_conversation_id' => $result['data']['OriginatorConversationID'] ?? null,
            'conversation_id' => $result['data']['ConversationID'] ?? null,
            'response_code' => $result['data']['ResponseCode'] ?? null,
            'response_description' => $result['data']['ResponseDescription'] ?? null,
            'request_payload' => [
                'phone' => $data['phone'],
                'amount' => $data['amount'],
                'remarks' => $data['remarks'] ?? null,
            ],
            'response_payload' => $result['data'] ?? $result,
        ]);

        return back()->with('mpesa_b2c_result', $result);
    }

    public function b2cResult(Request $request)
    {
        Log::info('M-Pesa B2C result received', $request->all());
        $payload = $request->all();

        MpesaCallback::create([
            'type' => 'b2c_result',
            'result_code' => data_get($payload, 'Result.ResultCode'),
            'result_desc' => data_get($payload, 'Result.ResultDesc'),
            'originator_conversation_id' => data_get($payload, 'Result.OriginatorConversationID'),
            'conversation_id' => data_get($payload, 'Result.ConversationID'),
            'transaction_id' => data_get($payload, 'Result.TransactionID'),
            'payload' => $payload,
        ]);

        return response()->json([
            'ResultCode' => 0,
            'ResultDesc' => 'Accepted',
        ]);
    }

    public function b2cTimeout(Request $request)
    {
        Log::info('M-Pesa B2C timeout received', $request->all());
        $payload = $request->all();

        MpesaCallback::create([
            'type' => 'b2c_timeout',
            'result_code' => data_get($payload, 'Result.ResultCode'),
            'result_desc' => data_get($payload, 'Result.ResultDesc'),
            'originator_conversation_id' => data_get($payload, 'Result.OriginatorConversationID'),
            'conversation_id' => data_get($payload, 'Result.ConversationID'),
            'transaction_id' => data_get($payload, 'Result.TransactionID'),
            'payload' => $payload,
        ]);

        return response()->json([
            'ResultCode' => 0,
            'ResultDesc' => 'Accepted',
        ]);
    }

    public function latestCallback()
    {
        $latestCallback = MpesaCallback::latest()->first();

        return response()->json([
            'ok' => ! empty($latestCallback),
            'data' => $latestCallback ? [
                'type' => $latestCallback->type,
                'payload' => $latestCallback->payload,
                'received_at' => $latestCallback->created_at?->toDateTimeString(),
            ] : null,
        ]);
    }
}
