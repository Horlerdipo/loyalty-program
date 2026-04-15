<?php

use App\Http\Controllers\V1\User\Auth\ForgotPasswordController;
use App\Http\Controllers\V1\User\Auth\LoginUserController;
use App\Http\Controllers\V1\User\Auth\LogoutUserController;
use App\Http\Controllers\V1\User\Auth\RegisterUserController;
use App\Http\Controllers\V1\User\Auth\ResetPasswordController;
use App\Http\Controllers\V1\User\PurchaseItemController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->as('auth:')->group(function () {
    Route::post('/register', RegisterUserController::class)
        ->name('register');

    Route::post('/login', LoginUserController::class)
        ->name('login');

    Route::post('/password/forgot', ForgotPasswordController::class)
        ->name('password:forgot');

    Route::post('/password/reset', ResetPasswordController::class)
        ->name('password:reset');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', function (Request $request) {
            return $request->user();
        });

        Route::post('/logout', LogoutUserController::class)
            ->name('logout');
    });
});


Route::middleware('auth:sanctum')->group(function () {
    Route::post('purchase', PurchaseItemController::class)->name('purchase');
});
