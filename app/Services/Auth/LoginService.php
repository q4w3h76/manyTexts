<?php

namespace App\Services\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginService
{
    public function login($credentials, $device_name)
    {
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken($device_name)->plainTextToken;
            return $token;
        } else {
            throw ValidationException::withMessages([
                'credentials' => ['The provided credentials are incorrect.'],
            ]);
        }
    }

    public function logout()
    {
        Auth::user()->currentAccessToken()->delete();
    }
}