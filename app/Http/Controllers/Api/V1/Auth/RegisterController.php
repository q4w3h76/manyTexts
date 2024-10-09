<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Jobs\UploadImageJob;
use App\Services\Auth\RegisterService;

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
            // upload image to local storage
            $path = $request->file('avatar')->store('images/avatars');
            $data['avatar_url'] = $path;
            // upload avatar to s3 cloud and delete from local storage via a queue
            UploadImageJob::dispatch($path);
        }
        
        $this->registerService->register($data);
        
        return response()->json([
            'status' => 'ok',
        ]);
    }
}

