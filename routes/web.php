<?php

use App\Http\Controllers\Web\Auth\LoginController;
use App\Http\Controllers\Web\Auth\LogoutController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::prefix('~admin')->group(function () {
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login');


    Route::middleware('auth:admin')->group(function () {
        Route::get('/', function () {
            return view('admin.pages.dashboard.index');
        });
        Route::get('/logout', LogoutController::class)->name('logout');
    });
});
