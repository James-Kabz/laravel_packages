<?php

return [
    'default' => env('SMS_DRIVER', 'africastalking'),

    'drivers' => [
        'africastalking' => [
            'username' => env('AFRICASTALKING_USERNAME', 'sandbox'),
            'api_key' => env('AFRICASTALKING_API_KEY'),
            'from' => env('AFRICASTALKING_FROM'),
            'endpoint' => env('AFRICASTALKING_SMS_ENDPOINT', 'https://api.africastalking.com/version1/messaging'),
            'timeout' => (int) env('AFRICASTALKING_TIMEOUT', 15),
            // Optional defaults that can be overridden per send() call.
            'bulk_mode' => env('AFRICASTALKING_BULK_MODE'),
        ],
    ],

    'templates' => [
        // 'compliance_notice' => 'Dear {name}, your status is {status}.',
    ],

    'logging' => [
        'enabled' => env('SMS_LOGGING', false),
        'table' => 'sms_logs',
    ],

    'webhook' => [
        'enabled' => env('SMS_WEBHOOK_ENABLED', false),
        'path' => env('SMS_WEBHOOK_PATH', 'sms/webhook'),
        'middleware' => ['api'],
    ],
];
