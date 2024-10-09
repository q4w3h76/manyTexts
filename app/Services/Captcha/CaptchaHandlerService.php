<?php

namespace App\Services\Captcha;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class CaptchaHandlerService implements CaptchaHandlerInterface
{
    public static function handle($data, string $type = 'default'): bool
    {
        $captcha = Config::get('captcha.' . $type);
        $response = Http::asForm()->post($captcha['endpoint'], [
            'secret' => $captcha['secret_key'],
            'response' => $data,
        ]);

        return $response->json()['success'] ?? false;
    }
}