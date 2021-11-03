<?php

use App\Http\Controllers\Api\v1\TransactionController;
use App\Http\Controllers\Api\v1\UserController;
use App\Http\Controllers\Api\v1\WalletController;
use Illuminate\Support\Facades\Route;

$api_v = 'api-v1';

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
Route::middleware('auth:api')->group(function () {
    /**
     * Пользователь
     */
    Route::prefix('user')->group(function () {
        Route::get('{id}', [UserController::class, 'get'])->name('api-v1-user-get');
    });
    /**
     * Кошелек
     */
    Route::prefix('wallet')->group(function () {
        Route::get('get', [WalletController::class, 'getList'])->name('api-v1-wallet-get-list');
        Route::get('get/{id}', [WalletController::class, 'getList'])->name('api-v1-wallet-get');
        Route::post('create', [WalletController::class, 'create'])->name('api-v1-wallet-create');
    });
    /**
     * Транзакция
     */
    Route::prefix('transaction')->group(function () {
        Route::get('get', [TransactionController::class, 'getList'])->name('api-v1-transaction-get-list');
        Route::get('get/{id}', [TransactionController::class, 'get'])->name('api-v1-transaction-get');
        Route::post('create', [TransactionController::class, 'create'])->name('api-v1-transaction-create');
        Route::post('accept', [TransactionController::class, 'accept'])->name('api-v1-transaction-accept');
        Route::prefix('commission')->group(function () {
            Route::get('get', [TransactionController::class, 'get'])->name('api-v1-transaction-commission-get');
            Route::get('calculate', [TransactionController::class, 'get'])->name('api-v1-transaction-commission-get');
        });
    });
});
