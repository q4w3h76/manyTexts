<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;

class RegisterService
{
    public function register($data)
    {
        $data['password'] = Hash::make($data['password']);
        
        $user = User::create($data);
        event(new Registered($user));
        return $user->id;
    }
}