<?php

namespace JamesKabz\Sms\Tests\Fakes;

use JamesKabz\Sms\Contracts\SmsDriver;

class FakeSmsDriver implements SmsDriver
{
    public function send(string|array $to, string $message, array $options = []): array
    {
        return [
            'success' => true,
            'status' => 200,
            'data' => [
                'to' => $to,
                'message' => $message,
                'options' => $options,
            ],
            'body' => '',
        ];
    }
}
