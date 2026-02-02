<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use JamesKabz\Sms\Facades\Sms;
use App\Http\Controllers\ComplianceNotificationController;
use App\Http\Controllers\MpesaTestController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// sms test route
Route::post('/send-sms', function (Request $request) {
    $data = $request->validate([
        'to' => 'required',
        'message' => 'required|string',
    ]);

    return Sms::send($data['to'], $data['message']);
});

// compliance notification example
Route::post('/compliance/notify', ComplianceNotificationController::class);

// mpesa test routes
Route::post('/mpesa/token', [MpesaTestController::class, 'token']);
Route::post('/mpesa/b2c/send', [MpesaTestController::class, 'b2c']);
Route::post('/mpesa/b2c/result', [MpesaTestController::class, 'b2cResult']);
Route::post('/mpesa/b2c/timeout', [MpesaTestController::class, 'b2cTimeout']);
Route::get('/mpesa/callback/latest', [MpesaTestController::class, 'latestCallback']);
