<?php

use App\Http\Controllers\Api\V1\TextController;
use App\Models\Text;
use Illuminate\Http\Request;
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
    Route::controller(TextController::class)->name('text.')->group(function () {
        Route::get('', 'index')->name('index');
        Route::post('', 'store')->name('store');
        Route::get('{text}', 'show')->name('show');
        Route::put('{text}', 'update')->name('update');
        Route::delete('{text}', 'destroy')->name('destroy');
    });
});
