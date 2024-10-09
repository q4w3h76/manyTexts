<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\Auth\RegisterService;
use App\Services\UploadImageService;

class RegisterController extends Controller
{
    private $registerService;

    public function __construct(RegisterService $registerService) {
        $this->registerService = $registerService;
    }

    public function __invoke(RegisterRequest $request)
    {
        $data = $request->except(['avatar']);
        
        if($request->hasFile('avatar')) {
            $path_to_avatars = 'images/avatars';
            $data['avatar_url'] = UploadImageService::upload($request->file('avatar'), $path_to_avatars);
        }
        
        $this->registerService->register($data);
        
        return response()->json([
            'status' => 'ok',
        ]);
    }
}

