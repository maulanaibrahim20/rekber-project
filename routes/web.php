<?php

use App\Http\Controllers\Web\AdministratorController;
use App\Http\Controllers\Web\Auth\LoginController;
use App\Http\Controllers\Web\Auth\LogoutController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::prefix('~admin')->group(function () {
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login');


    Route::middleware('auth:admin')->group(function () {

        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::prefix('administrator')->group(function () {
            Route::group(['prefix' => 'account', 'controller' => AdministratorController::class], function () {
                Route::get('/', 'index')->name('administrator.account');
                Route::get('/getData', 'getData')->name('administrator.getData');
                Route::get('/create',  'create')->name('administrator.create');
                Route::post('/store',  'store')->name('administrator.store');
                Route::get('/edit/{id}',  'edit')->name('administrator.edit');
                Route::put('/update/{id}',  'update')->name('administrator.update');
                Route::delete('/destroy/{id}',  'destroy')->name('administrator.destroy');
            });
        });

        Route::group(['prefix' => 'user', 'controller' => UserController::class], function () {
            Route::get('/', 'index')->name('user');
            Route::get('/getData', 'getData')->name('user.getData');
            Route::get('/create',  'create')->name('user.create');
            Route::post('/store',  'store')->name('user.store');
            Route::get('/edit/{id}',  'edit')->name('user.edit');
            Route::put('/update/{id}',  'update')->name('user.update');
            Route::delete('/destroy/{id}',  'destroy')->name('user.destroy');
        });

        Route::get('/logout', LogoutController::class)->name('logout');
    });
});
