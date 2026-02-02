<?php

namespace JamesKabz\MpesaPkg;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class MpesaClient
{
    /**
     * @var array<string, mixed>
     */
    protected array $config;

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @return array<string, mixed>
     */
    public function getAccessToken(): array
    {
        $consumerKey = $this->config['consumer_key'] ?? null;
        $consumerSecret = $this->config['consumer_secret'] ?? null;

        if (! $consumerKey || ! $consumerSecret) {
            return [
                'ok' => false,
                'error' => 'Missing MPESA_CONSUMER_KEY or MPESA_CONSUMER_SECRET.',
            ];
        }

        $baseUrl = rtrim($this->config['base_url'] ?? 'https://sandbox.safaricom.co.ke', '/');
        $url = $baseUrl . '/oauth/v1/generate';

        $response = Http::timeout(15)
            ->withBasicAuth($consumerKey, $consumerSecret)
            ->get($url, [
                'grant_type' => 'client_credentials',
            ]);

        $json = $response->json();

        return [
            'ok' => $response->successful(),
            'status' => $response->status(),
            'data' => is_array($json) ? $json : null,
            'body' => $response->successful() ? null : $response->body(),
        ];
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function stkPush(array $payload): array
    {
        $tokenResult = $this->getAccessToken();
        $accessToken = $tokenResult['data']['access_token'] ?? null;

        if (! $tokenResult['ok'] || ! $accessToken) {
            return [
                'ok' => false,
                'error' => 'Failed to get access token.',
                'token_result' => $tokenResult,
            ];
        }

        $stkConfig = $this->config['credentials']['stk'] ?? [];
        $shortCode = $stkConfig['short_code'] ?? null;
        $passkey = $stkConfig['passkey'] ?? null;

        if (! $shortCode || ! $passkey) {
            return [
                'ok' => false,
                'error' => 'Missing MPESA_STK_SHORT_CODE or MPESA_STK_PASSKEY.',
            ];
        }

        $timestamp = $payload['timestamp'] ?? now()->format('YmdHis');
        $password = base64_encode($shortCode . $passkey . $timestamp);

        $data = [
            'BusinessShortCode' => $shortCode,
            'Password' => $password,
            'Timestamp' => $timestamp,
            'TransactionType' => $payload['transaction_type'] ?? ($stkConfig['transaction_type'] ?? 'CustomerPayBillOnline'),
            'Amount' => $payload['amount'] ?? 1,
            'PartyA' => $payload['phone'],
            'PartyB' => $payload['party_b'] ?? $shortCode,
            'PhoneNumber' => $payload['phone'],
            'CallBackURL' => $payload['callback_url'] ?? ($stkConfig['callback_url'] ?? ''),
            'AccountReference' => $payload['account_reference'] ?? ($stkConfig['account_reference'] ?? 'Mpesa Test'),
            'TransactionDesc' => $payload['transaction_desc'] ?? ($stkConfig['transaction_desc'] ?? 'STK Push Test'),
        ];

        $baseUrl = rtrim($this->config['base_url'] ?? 'https://sandbox.safaricom.co.ke', '/');
        $url = $baseUrl . '/mpesa/stkpush/v1/processrequest';

        $response = Http::timeout(20)
            ->withToken($accessToken)
            ->post($url, $data);

        $json = $response->json();

        return [
            'ok' => $response->successful(),
            'status' => $response->status(),
            'data' => is_array($json) ? $json : null,
            'body' => $response->successful() ? null : $response->body(),
        ];
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function b2c(array $payload): array
    {
        $tokenResult = $this->getAccessToken();
        $accessToken = $tokenResult['data']['access_token'] ?? null;

        if (! $tokenResult['ok'] || ! $accessToken) {
            return [
                'ok' => false,
                'error' => 'Failed to get access token.',
                'token_result' => $tokenResult,
            ];
        }

        $b2cConfig = $this->config['credentials']['b2c'] ?? [];
        $shortCode = $b2cConfig['short_code'] ?? null;
        $initiator = $b2cConfig['initiator_name'] ?? null;
        $securityCredential = $b2cConfig['security_credential'] ?? null;

        if (! $shortCode || ! $initiator || ! $securityCredential) {
            return [
                'ok' => false,
                'error' => 'Missing MPESA_B2C_SHORT_CODE, MPESA_B2C_INITIATOR, or MPESA_B2C_SECURITY_CREDENTIAL.',
            ];
        }

        $data = [
            'InitiatorName' => $initiator,
            'SecurityCredential' => $securityCredential,
            'CommandID' => $b2cConfig['command_id'] ?? 'BusinessPayment',
            'Amount' => $payload['amount'] ?? 1,
            'PartyA' => $shortCode,
            'PartyB' => $payload['phone'],
            'Remarks' => $payload['remarks'] ?? 'B2C Payment',
            'QueueTimeOutURL' => $b2cConfig['timeout_url'] ?? '',
            'ResultURL' => $b2cConfig['result_url'] ?? '',
            'Occasion' => $payload['occasion'] ?? 'Mpesa Test',
            'OriginatorConversationID' => $payload['originator_conversation_id'] ?? (string) Str::uuid(),
        ];

        $baseUrl = rtrim($this->config['base_url'] ?? 'https://sandbox.safaricom.co.ke', '/');
        $url = $baseUrl . '/mpesa/b2c/v3/paymentrequest';

        $response = Http::timeout(20)
            ->withToken($accessToken)
            ->post($url, $data);

        $json = $response->json();

        return [
            'ok' => $response->successful(),
            'status' => $response->status(),
            'data' => is_array($json) ? $json : null,
            'body' => $response->successful() ? null : $response->body(),
        ];
    }
}
