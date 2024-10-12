<?php

use App\Http\Controllers\Api\V1\Auth\EmailVerificationController;
use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\V1\Auth\RegisterController;
use App\Http\Controllers\Api\V1\TextController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v1')->group(function () {
    // auth routes
    Route::name('auth.')->group(function () {
        // login routes
        Route::controller(LoginController::class)->group(function () {
            Route::post('login', 'login')->name('login')->middleware('guest');
            Route::post('logout', 'logout')->name('logout')->middleware('auth:sanctum');
            Route::get('me', 'me')->name('me')->middleware('auth:sanctum');
        });
        // register route
        Route::post('register', RegisterController::class)->name('register')->middleware('guest');
    });
    // email verification routes
    Route::controller(EmailVerificationController::class)
        ->prefix('email/verify')
        ->name('verification.')
        ->middleware(['auth:sanctum', 'notVerified'])
        ->group(function () {
            Route::post('notice', 'notice')->name('notice');
            Route::get('{id}/{hash}', 'verify')->name('verify')->middleware('signed');
        });
    // text resource routes
    Route::controller(TextController::class)->middleware('auth.optional')->name('text.')->group(function () {
        Route::get('', 'index')->name('index');
        Route::post('', 'store')->name('store');
        Route::get('{text}', 'show')->name('show')->can('view', 'text');
        Route::patch('{text}', 'update')->name('update')->can('update', 'text')->middleware('verified');
        Route::delete('{text}', 'destroy')->name('destroy')->can('delete', 'text')->middleware('verified');
    });
});
