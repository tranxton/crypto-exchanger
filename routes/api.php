<?php

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

Route::middleware('api')->prefix('v1')->group(function () {
    Route::prefix('user')->group(function () {
        Route::post('register', [\App\Http\Controllers\Api\v1\UserController::class, 'register'])->name('api-v1-user-register');
        Route::get('login', [\App\Http\Controllers\Api\v1\UserController::class, 'login'])->name('api-v1-user-login');
        Route::middleware('auth:api')->group(function () {
            Route::get('{id}', [\App\Http\Controllers\Api\v1\UserController::class, 'get'])->name('api-v1-user-get');
        });
    });
});
