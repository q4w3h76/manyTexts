<?php

namespace App\Services\Captcha;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

interface CaptchaHandlerInterface 
{
    public static function handle($data, string $type = 'default');
}