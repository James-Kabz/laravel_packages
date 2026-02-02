<?php

return [
    'consumer_key' => env('MPESA_CONSUMER_KEY'),
    'consumer_secret' => env('MPESA_CONSUMER_SECRET'),
    'base_url' => env('MPESA_BASE_URL', 'https://sandbox.safaricom.co.ke'),

    'credentials' => [
        'b2c' => [
            'initiator_name' => env('MPESA_B2C_INITIATOR'),
            'security_credential' => env('MPESA_B2C_SECURITY_CREDENTIAL'),
            'short_code' => env('MPESA_B2C_SHORT_CODE'),
            'command_id' => env('MPESA_B2C_COMMAND_ID', 'BusinessPayment'),
            'timeout_url' => env('MPESA_B2C_TIMEOUT_URL'),
            'result_url' => env('MPESA_B2C_RESULT_URL'),
            'passkey' => env('MPESA_B2C_PASSKEY'),
        ],
        'stk' => [
            'short_code' => env('MPESA_STK_SHORT_CODE'),
            'passkey' => env('MPESA_STK_PASSKEY'),
            'callback_url' => env('MPESA_STK_CALLBACK_URL'),
            'transaction_type' => env('MPESA_STK_TRANSACTION_TYPE', 'CustomerPayBillOnline'),
            'account_reference' => env('MPESA_STK_ACCOUNT_REFERENCE', 'Mpesa Test'),
            'transaction_desc' => env('MPESA_STK_TRANSACTION_DESC', 'STK Push Test'),
        ],
    ],
];
