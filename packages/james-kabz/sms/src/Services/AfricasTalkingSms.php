<?php

namespace JamesKabz\Sms\Services;

use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use JamesKabz\Sms\Contracts\SmsDriver;

class AfricasTalkingSms implements SmsDriver
{
    public function __construct(private array $config)
    {
    }

    public function send(string|array $to, string $message, array $options = []): array
    {
        $this->guardConfig();

        $payload = array_merge([
            'username' => $this->config['username'],
            'to' => $this->formatRecipients($to),
            'message' => $message,
        ], $this->defaultOptions(), $options);

        if (!empty($this->config['from']) && empty($payload['from'])) {
            $payload['from'] = $this->config['from'];
        }

        $response = Http::timeout($this->config['timeout'] ?? 15)
            ->asForm()
            ->withHeaders([
                'apiKey' => $this->config['api_key'],
                'Accept' => 'application/json',
            ])
            ->post($this->config['endpoint'], $payload);

        if ($response instanceof PromiseInterface) {
            $response = $response->wait();
        }

        return [
            'success' => $response->successful(),
            'status' => $response->status(),
            'data' => $response->json(),
            'body' => $response->body(),
        ];
    }

    private function guardConfig(): void
    {
        if (empty($this->config['username'])) {
            throw new InvalidArgumentException('sms.username is required.');
        }

        if (empty($this->config['api_key'])) {
            throw new InvalidArgumentException('sms.api_key is required.');
        }

        if (empty($this->config['endpoint'])) {
            throw new InvalidArgumentException('sms.endpoint is required.');
        }
    }

    private function formatRecipients(string|array $to): string
    {
        if (is_array($to)) {
            $to = array_filter(array_map('trim', $to));
            return implode(',', $to);
        }

        return trim($to);
    }

    private function defaultOptions(): array
    {
        $options = [];

        if ($this->config['bulk_mode'] !== null && $this->config['bulk_mode'] !== '') {
            $options['bulkSMSMode'] = (int) $this->config['bulk_mode'];
        }

        return $options;
    }
}
