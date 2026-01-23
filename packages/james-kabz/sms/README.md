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

SMS_DRIVER=africastalking
SMS_LOGGING=true
SMS_WEBHOOK_ENABLED=false
SMS_WEBHOOK_PATH=sms/webhook
```

## Usage

### Facade

```php
use JamesKabz\Sms\Facades\Sms;

Sms::send('+2547XXXXXXXX', 'Hello from Africa\'s Talking');
```

### Drivers

Set the default driver in `.env`:

```
SMS_DRIVER=africastalking
```

To add another driver, define it in `config/sms.php` under `drivers` with a `class` key that implements `JamesKabz\\Sms\\Contracts\\SmsDriver`, then set `SMS_DRIVER` to that name.

### Send with a template

```php
// config/sms.php -> templates['compliance_notice']
Sms::sendTemplate('compliance_notice', '+2547XXXXXXXX', [
    'name' => 'Amina',
    'status' => 'COMPLIANT',
]);
```

### Dependency Injection

```php
use JamesKabz\Sms\Services\SmsManager;

public function send(SmsManager $sms)
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

## Notifications (SmsChannel)

```php
use Illuminate\\Notifications\\Notification;
use JamesKabz\\Sms\\Notifications\\SmsChannel;

class ComplianceNotice extends Notification
{
    public function via($notifiable): array
    {
        return [SmsChannel::class];
    }

    public function toSms($notifiable): array
    {
        return [
            'template' => 'compliance_notice',
            'data' => ['name' => $notifiable->name, 'status' => $notifiable->status],
        ];
    }
}
```

To resolve the phone number, implement `routeNotificationForSms()` on your notifiable or provide a `phone` / `phone_number` field.

## Logging

When `SMS_LOGGING=true`, send attempts are stored in the `sms_logs` table.
Run migrations in your app:

```bash
php artisan migrate
```

## Webhook (delivery reports)

Enable in `.env`:

```
SMS_WEBHOOK_ENABLED=true
SMS_WEBHOOK_PATH=sms/webhook
```

This registers a POST endpoint that stores the payload in `sms_logs` (when logging is enabled).

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

## Testing (package)

```bash
cd packages/james-kabz/sms
composer install
vendor/bin/phpunit
```

## License

MIT
