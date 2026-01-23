<?php

namespace JamesKabz\Sms\Contracts;

interface SmsDriver
{
    public function send(string|array $to, string $message, array $options = []): array;
}
