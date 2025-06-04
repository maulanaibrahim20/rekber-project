<?php

use App\Http\Controllers\Api\Auth\ForgotPasswordController;
use App\Http\Controllers\Api\Auth\LoginController as AuthLoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\ResetPasswordController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {

    Route::get('email/verify/{id}/{hash}', [RegisterController::class, 'verifyEmail'])
        ->middleware('signed')
        ->name('verification.verify');

    Route::post('email/resend', [RegisterController::class, 'resendVerificationEmail']);

    Route::post('/forgot-password', [ForgotPasswordController::class, 'forgotPassword'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'resetPassword']);
    Route::post('/login', [AuthLoginController::class, 'login']);
    Route::post('/register', [RegisterController::class, 'register']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::get('check', [AuthLoginController::class, 'checkAuth']);
    });

    Route::get('/user', [UserController::class, 'index']);
    Route::put('/user/update', [UserController::class, 'update']);


    Route::post('/logout', [LogoutController::class, 'logout']);
});
