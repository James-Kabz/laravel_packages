<?php

namespace JamesKabz\Sms\Facades;

use Illuminate\Support\Facades\Facade;
use JamesKabz\Sms\Services\AfricasTalkingSms;

class Sms extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return AfricasTalkingSms::class;
    }
}
