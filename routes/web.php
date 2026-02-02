<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MpesaTestController;

Route::get('/', function () {
    return view('welcome');
});

// Route::get('/mpesa/test', [MpesaTestController::class, 'index']);
// Route::post('/mpesa/test/token', [MpesaTestController::class, 'token']);
// Route::post('/mpesa/test/stk', [MpesaTestController::class, 'stkPush']);
// Route::post('/mpesa/callback/stk', [MpesaTestController::class, 'stkCallback']);
// Route::post('/mpesa/test/b2c', [MpesaTestController::class, 'b2c']);
// Route::post('/mpesa/b2c/result', [MpesaTestController::class, 'b2cResult']);
// Route::post('/mpesa/b2c/timeout', [MpesaTestController::class, 'b2cTimeout']);
