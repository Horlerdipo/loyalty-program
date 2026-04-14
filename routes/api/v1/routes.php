<?php

use App\Http\Controllers\V1\User\Auth\LoginUserController;
use App\Http\Controllers\V1\User\Auth\RegisterUserController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->as('auth:')->group(function () {
    Route::post('/register', RegisterUserController::class)
        ->name('register');

    Route::post('/login', LoginUserController::class)
        ->name('login');
});
