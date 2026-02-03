<?php

namespace JamesKabz\MpesaPkg\Models;

use Illuminate\Database\Eloquent\Model;

class MpesaRequest extends Model
{
    protected $fillable = [
        'type',
        'phone',
        'amount',
        'remarks',
        'originator_conversation_id',
        'conversation_id',
        'response_code',
        'response_description',
        'request_payload',
        'response_payload',
    ];

    protected $casts = [
        'request_payload' => 'array',
        'response_payload' => 'array',
        'amount' => 'decimal:2',
    ];
}
