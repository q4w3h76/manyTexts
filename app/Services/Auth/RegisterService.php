<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RegisterService
{
    public function register($data)
    {
        $data['password'] = Hash::make($data['password']);
        
        $user = User::create($data);

        return $user->id;
    }
}