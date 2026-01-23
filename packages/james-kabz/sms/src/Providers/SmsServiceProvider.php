<?php

namespace JamesKabz\Sms\Providers;

use Illuminate\Support\ServiceProvider;
use JamesKabz\Sms\Services\AfricasTalkingSms;

class SmsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../Config/sms.php', 'sms');

        $this->app->singleton(AfricasTalkingSms::class, function ($app) {
            return new AfricasTalkingSms($app['config']['sms'] ?? []);
        });

        $this->app->alias(AfricasTalkingSms::class, 'sms');
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../Config/sms.php' => config_path('sms.php'),
        ], 'sms-config');
    }
}
