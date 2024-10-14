<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use App\Services\Auth\LoginService;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    private $loginService;

    public function __construct(LoginService $loginService)
    {
        $this->loginService = $loginService;
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only(['email', 'password']);
        $device_name = $request->input('device_name');

        $token = $this->loginService->login($credentials, $device_name);

        return response()->json([
            'token' => $token
        ]);
    }

    public function logout()
    {
        $this->loginService->logout();

        return response()->json([], 204);
    }

    public function me(Request $request)
    {
        return UserResource::make($request->user());
    }
}
