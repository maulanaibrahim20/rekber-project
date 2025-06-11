<?php

use App\Http\Controllers\Api\Auth\ForgotPasswordController;
use App\Http\Controllers\Api\Auth\LoginController as AuthLoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\ResetPasswordController;
use App\Http\Controllers\Api\FaqCategoryController;
use App\Http\Controllers\Api\LikeAndCommentController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProfileController;
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

Route::middleware('accept.json')->group(function () {

    Route::get('/product/public', [ProductController::class, 'index']);
    Route::get('/product/{uuid}/public', [ProductController::class, 'show']);
    Route::get('/faq', [FaqCategoryController::class, 'index']);
    Route::get('/faq/{slug}', [FaqCategoryController::class, 'show']);
    Route::get('/profile/{username}', [ProfileController::class, 'index']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::prefix('auth')->group(function () {
            Route::get('check', [AuthLoginController::class, 'checkAuth']);
        });

        Route::group(['prefix' => 'product', 'controller' => ProductController::class], function () {
            Route::get('/', [ProductController::class, 'index']);
            Route::post('/store', 'store');
            Route::get('/{uuid}', 'show');
            Route::get('/{uuid}/edit', 'edit');
            Route::put('/{uuid}/update', 'update');
            Route::delete('/{uuid}/delete', 'destroy');

            Route::delete('/{id}/delete/image', 'deleteImage');
            Route::post('/{uuid}/pin', [ProfileController::class, 'pin']);

            Route::group(['controller' => LikeAndCommentController::class], function () {
                Route::post('/{uuid}/like', 'toggleLike');
                Route::post('/{uuid}/comments', 'comment');
            });
        });

        Route::get('/user', [UserController::class, 'index']);
        Route::put('/user/update', [UserController::class, 'update']);


        Route::post('/logout', [LogoutController::class, 'logout']);
    });
});
