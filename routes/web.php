<?php

use App\Http\Controllers\Web\AdministratorController;
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
        })->name('dashboard');

        Route::prefix('administrator')->group(function () {
            Route::group(['prefix' => 'account', 'controller' => AdministratorController::class], function () {
                Route::get('/', 'index')->name(name: 'administrator.account');
                Route::get('/getData', 'getData')->name(name: 'administrator.getData');
                Route::get('/create',  'create')->name(name: 'administrator.create');
                Route::post('/store',  'store')->name(name: 'administrator.store');
                Route::get('/edit/{id}',  'edit')->name(name: 'administrator.edit');
                Route::put('/update/{id}',  'update')->name(name: 'administrator.update');
                Route::delete('/destroy/{id}',  'destroy')->name(name: 'administrator.destroy');
            });
        });

        Route::get('/logout', LogoutController::class)->name('logout');
    });
});
