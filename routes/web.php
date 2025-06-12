<?php

use App\Http\Controllers\Web\AdministratorController;
use App\Http\Controllers\Web\Auth\LoginController;
use App\Http\Controllers\Web\Auth\LogoutController;
use App\Http\Controllers\Web\BankAccountController;
use App\Http\Controllers\Web\BankController;
use App\Http\Controllers\Web\Config\AssignPermissionController;
use App\Http\Controllers\Web\Config\PermissionController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\FaqCategoryController;
use App\Http\Controllers\Web\FaqController;
use App\Http\Controllers\Web\LogApiController;
use App\Http\Controllers\Web\ProductController;
use App\Http\Controllers\Web\TagController;
use App\Http\Controllers\Web\UserController;
use Illuminate\Support\Facades\Route;
use Rap2hpoutre\LaravelLogViewer\LogViewerController;

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

        Route::group(['prefix' => 'product', 'controller' => ProductController::class], function () {
            Route::get('', 'index')->name('product');
            Route::get('/getData', 'getData')->name('product.getData');
            Route::get('/show/{uuid}', 'show')->name('product.show');
            Route::put('/update/status/{uuid}', 'updateStatus')->name('product.updateStatus');
        });

        Route::group(['prefix' => 'tag', 'controller' => TagController::class], function () {
            Route::get('', 'index')->name('tag');
            Route::get('/getData', 'getData')->name('tag.getData');
            Route::get('/create', 'create')->name('tag.create');
            Route::post('/store', 'store')->name('tag.assign');
        });

        Route::prefix('config')->name('config.')->group(function () {
            Route::group(['prefix' => 'permission', 'controller' => PermissionController::class], function () {
                Route::get('', 'index')->name('permission');
                Route::get('/getData', 'getData')->name('permission.getData');
                Route::get('/create', 'create')->name('permission.create');
                Route::post('/store',  'store')->name('permission.store');
                Route::get('/edit/{id}',  'edit')->name('permission.edit');
                Route::put('/update/{id}',  'update')->name('permission.update');
                Route::delete('/destroy/{id}',  'destroy')->name('permission.destroy');
            });

            Route::group(['prefix' => 'assign-permission', 'controller' => AssignPermissionController::class], function () {
                Route::get('', 'index')->name('assign');
                Route::get('/getData', 'getData')->name('assign.getData');
                Route::get('/create/{id}', 'create')->name('assign.create');
                Route::post('/assign', 'assignPermission')->name('assign.assign');
                Route::post('/revoke', 'revokePermission')->name('assign.revoke');
            });

            Route::get('/log', [LogViewerController::class, 'index'])->name('log');
        });

        Route::group(['prefix' => 'bank', 'controller' => BankController::class], function () {
            Route::get('/', 'index')->name('bank');
            Route::get('/getData', 'getData')->name('bank.getData');
            Route::post('/update-status', 'updateStatus')->name('bank.updateStatus');
            Route::get('/create', 'create')->name('bank.create');
            Route::post('/store', 'store')->name('bank.store');
            Route::delete('/destroy/{id}', 'destroy')->name('bank.destroy');

            Route::group(['prefix' => 'account', 'controller' => BankAccountController::class], function () {
                Route::get('/', 'index')->name('bank.account');
                Route::get('/getData', 'getData')->name('bank.account.getData');
                Route::get('/create', 'create')->name('bank.account.create');
                Route::post('/store', 'store')->name('bank.account.store');
                Route::get('/edit/{id}', 'edit')->name('bank.account.edit');
                Route::put('/update/{id}', 'update')->name('bank.account.update');
                Route::delete('/destroy/{id}', 'destroy')->name('bank.account.destroy');
            });
        });

        Route::group(['prefix' => 'content-management'], function () {
            Route::group(['prefix' => 'faq-category', 'controller' => FaqCategoryController::class], function () {
                Route::get('/', 'index')->name('faq.category');
                Route::get('/getData', 'getData')->name('faq.category.getData');
                Route::get('/create', 'create')->name('faq.category.create');
                Route::post('/store', 'store')->name('faq.category.store');
                Route::get('/{slug}', 'show')->name('faq.category.show');
                Route::get('/edit/{slug}', 'edit')->name('faq.category.edit');
                Route::put('/update/{id}', 'update')->name('faq.category.update');
                Route::delete('/destroy/{id}', 'destroy')->name('faq.category.destroy');
            });

            Route::group(['prefix' => 'faq', 'controller' => FaqController::class], function () {
                Route::get('{slug}/getData', 'getData')->name('faq.getData');
                Route::get('{slug}/create', 'create')->name('faq.create');
                Route::post('{slug}/store', 'store')->name('faq.store');
                Route::get('/edit/{id}', 'edit')->name('faq.edit');
                Route::put('/update/{id}', 'update')->name('faq.update');
                Route::delete('/destroy/{id}', 'destroy')->name('faq.destroy');
            });
        });

        Route::get('/logout', LogoutController::class)->name('logout');
    });
});
