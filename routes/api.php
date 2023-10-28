<?php

use App\Http\Controllers\Api;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::name('api.')->group(function(){
    Route::prefix('account')->group(function(){
        Route::post('', [Api\AccountController::class, 'store'])->name('account.store');

        Route::prefix('{account}')->group(function(){
            Route::get('', [Api\AccountController::class, 'show'])->name('account.show');
            Route::prefix('pix')->name('pix.')->group(function(){
                Route::post('', [Api\PixKeyController::class, 'store'])->name('store');
                Route::get('', [Api\AccountController::class, 'pixKeys'])->name('index');
            });

            Route::prefix('transaction')->name('transaction.')->group(function(){
                Route::get('', [Api\AccountController::class, 'transaction'])->name('index');
                Route::post('', [Api\TransactionController::class, 'store'])->name('store');
                Route::get('{transaction}', [Api\TransactionController::class, 'show'])->name('show');
            });
        });
    });
});
