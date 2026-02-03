<?php

use Illuminate\Support\Facades\Route;
use JamesKabz\MpesaPkg\Http\Controllers\MpesaC2bController;
use JamesKabz\MpesaPkg\Http\Controllers\MpesaB2cController;
use JamesKabz\MpesaPkg\Http\Controllers\MpesaStkController;

Route::prefix(config('mpesa.route_prefix', 'mpesa'))
    ->middleware(config('mpesa.route_middleware', ['api']))
    ->group(function () {
        Route::post('stk/push', [MpesaStkController::class, 'push']);
        Route::post('stk/callback', [MpesaStkController::class, 'callback']);
        Route::post('b2c/send', [MpesaB2cController::class, 'send']);
        Route::post('b2c/result', [MpesaB2cController::class, 'result']);
        Route::post('b2c/timeout', [MpesaB2cController::class, 'timeout']);
        Route::post('c2b/validation', [MpesaC2bController::class, 'validation']);
        Route::post('c2b/confirmation', [MpesaC2bController::class, 'confirmation']);
    });
