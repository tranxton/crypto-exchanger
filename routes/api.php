<?php

use App\Http\Controllers\Api\v1\BillController;
use App\Http\Controllers\Api\v1\CurrencyController;
use App\Http\Controllers\Api\v1\UserController;
use App\Http\Controllers\Api\v1\WalletController;
use Illuminate\Support\Facades\Route;

/**
 * Неавторизованный доступ
 */

Route::middleware('api')->prefix('v1')->group(function () {
    /**
     * Пользователь
     */
    Route::prefix('user')->group(function () {
        Route::post('register', [UserController::class, 'register'])->name('api-v1-user-register');
        Route::get('login', [UserController::class, 'login'])->name('api-v1-user-login');
    });
});

/**
 * Авторизованный доступ
 */
Route::middleware(['auth:api'])->prefix('v1')->group(function () {
    /**
     * Пользователь
     */
    Route::prefix('user')->group(function () {
        Route::get('{name}', [UserController::class, 'get'])->name('api-v1-user-get');
    });

    /**
     * Валюта
     */
    Route::prefix('currency')->group(function () {
        Route::get('all', [CurrencyController::class, 'getList'])->name('api-v1-currency-get-all');
    });

    /**
     * Кошелек
     */
    Route::prefix('wallet')->group(function () {
        Route::get('all', [WalletController::class, 'getList'])->name('api-v1-wallet-get-all');
        Route::get('{address}', [WalletController::class, 'get'])->name('api-v1-wallet-get');
        Route::post('', [WalletController::class, 'create'])->name('api-v1-wallet-create');
    });

    /**
     * Переводы
     */
    Route::prefix('bill')->group(function () {
        Route::get('all', [BillController::class, 'getList'])->name('api-v1-bill-get-all');
        Route::get('{id}', [BillController::class, 'get'])->name('api-v1-bill-get');
        Route::post('', [BillController::class, 'create'])->name('api-v1-bill-create');
//        Route::post('accept', [BillController::class, 'accept'])->name('api-v1-bill-accept');
//        Route::prefix('commission')->group(function () {
//            Route::get('calculate', [BillController::class, 'get'])->name('api-v1-bill-commission-get');
//        });
    });
});
