<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use JamesKabz\Sms\Facades\Sms;
use App\Http\Controllers\ComplianceNotificationController;

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
