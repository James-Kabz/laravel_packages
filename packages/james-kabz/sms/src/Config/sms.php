<?php

return [
    'username' => env('AFRICASTALKING_USERNAME', 'sandbox'),
    'api_key' => env('AFRICASTALKING_API_KEY'),
    'from' => env('AFRICASTALKING_FROM'),
    'endpoint' => env('AFRICASTALKING_SMS_ENDPOINT', 'https://api.africastalking.com/version1/messaging'),
    'timeout' => (int) env('AFRICASTALKING_TIMEOUT', 15),
    // Optional defaults that can be overridden per send() call.
    'bulk_mode' => env('AFRICASTALKING_BULK_MODE'),
];
