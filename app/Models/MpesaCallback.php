<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MpesaCallback extends Model
{
    protected $fillable = [
        'type',
        'result_code',
        'result_desc',
        'originator_conversation_id',
        'conversation_id',
        'transaction_id',
        'payload',
    ];

    protected $casts = [
        'payload' => 'array',
        'result_code' => 'integer',
    ];
}
