<?php

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
    Route::name('auth.')->group(function () {
        Route::controller(LoginController::class)->group(function () {
            Route::post('login', 'login')->name('login')->middleware('guest');
            Route::post('logout', 'logout')->name('logout')->middleware('auth:sanctum');
            Route::get('me', 'me')->name('me')->middleware('auth:sanctum');
        });
        Route::post('register', RegisterController::class)->name('register')->middleware('guest');
    });

    Route::controller(TextController::class)->middleware('auth.optional')->name('text.')->group(function () {
        Route::get('', 'index')->name('index');
        Route::post('', 'store')->name('store');
        Route::get('{text}', 'show')->name('show')->can('view', 'text');
        Route::patch('{text}', 'update')->name('update')->can('update', 'text');
        Route::delete('{text}', 'destroy')->name('destroy')->can('delete', 'text');
    });
});
