<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{
    public function notice(Request $request) {
        $request->user()->sendEmailVerificationNotification();

        return response()->json([], 200);
    }
    
    public function verify(EmailVerificationRequest $request) {
        $request->fulfill();

        return response()->json([], 200);
    }
}
