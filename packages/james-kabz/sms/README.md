# james-kabz/sms

Laravel package for sending SMS via Africa's Talking.

## Requirements

- PHP 8.1+
- Laravel 10/11/12

## Installation (local path)

1) Add repository and require the package in your app `composer.json`:

```json
{
  "repositories": [
    {
      "type": "path",
      "url": "packages/james-kabz/sms"
    }
  ],
  "require": {
    "james-kabz/sms": "*"
  }
}
```

2) Install:

```bash
composer update james-kabz/sms
```

## Configuration

Publish the config (optional):

```bash
php artisan vendor:publish --tag=sms-config
```

Add credentials to your `.env`:

```
AFRICASTALKING_USERNAME=your_username
AFRICASTALKING_API_KEY=your_api_key
AFRICASTALKING_FROM=your_sender_id
```

Optional overrides:

```
AFRICASTALKING_SMS_ENDPOINT=https://api.africastalking.com/version1/messaging
AFRICASTALKING_TIMEOUT=15
AFRICASTALKING_BULK_MODE=1
```

## Usage

### Facade

```php
use JamesKabz\Sms\Facades\Sms;

Sms::send('+2547XXXXXXXX', 'Hello from Africa\'s Talking');
```

### Dependency Injection

```php
use JamesKabz\Sms\Services\AfricasTalkingSms;

public function send(AfricasTalkingSms $sms)
{
    $sms->send(['+2547XXXXXXXX', '+2547YYYYYYYY'], 'Hello!');
}
```

### Per-call options

```php
Sms::send('+2547XXXXXXXX', 'Hello', [
    'from' => 'MyBrand',
    'bulkSMSMode' => 1,
]);
```

## Response format

`send()` returns a structured array:

```php
[
    'success' => true,
    'status' => 201,
    'data' => [...],
    'body' => '...'
]
```

## Notes

- Ensure your sender ID (`AFRICASTALKING_FROM`) is approved in Africa's Talking.
- For production, use your live username and API key (not the sandbox).

## License

MIT
