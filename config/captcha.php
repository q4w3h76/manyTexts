<?php

return [
    'default' => [
        'site_key' => env('CAPTCHA_SITE_KEY'),
        'secret_key' => env('CAPTCHA_SECRET_KEY'),
        'endpoint' => env('CAPTCHA_ENDPOINT'),
    ],
    
    'xcaptcha' => [
        'site_key' => env('CAPTCHA_SITE_KEY'),
        'secret_key' => env('CAPTCHA_SECRET_KEY'),
        'endpoint' => env('CAPTCHA_ENDPOINT') . '/' . env('CAPTCHA_SITE_KEY'),
    ],
];