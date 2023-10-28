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
        Route::prefix('{account}')->group(function(){
            Route::post('transaction', [Api\TransactionController::class, 'store'])->name('transaction.store');
            Route::post('pix', [Api\PixKeyController::class, 'store'])->name('pix.store');
        });
    });
});
