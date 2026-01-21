<?php

namespace JamesKabz\MpesaPkg\Providers;

use Illuminate\Support\ServiceProvider;

class MpesaServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../Config/mpesa.php', 'mpesa');
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../Config/mpesa.php' => config_path('mpesa.php'),
        ], 'mpesa-config');

        // load migrations
        $this->loadMigrationsFrom(__DIR__ . '../../database/migrations');
    }
}
