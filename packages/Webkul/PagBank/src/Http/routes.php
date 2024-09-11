<?php

use Illuminate\Support\Facades\Route;
use Webkul\PagBank\Http\Controllers\SmartButtonController;
use Webkul\PagBank\Http\Controllers\StandardController;

Route::group(['middleware' => ['web']], function () {
    Route::prefix('pagbank/standard')->group(function () {
        Route::get('/redirect', [StandardController::class, 'redirect'])->name('pagbank.standard.redirect');

        Route::get('/success', [StandardController::class, 'success'])->name('pagbank.standard.success');

        Route::get('/cancel', [StandardController::class, 'cancel'])->name('pagbank.standard.cancel');
    });

    Route::prefix('pagbank/smart-button')->group(function () {
        Route::get('/create-order', [SmartButtonController::class, 'createOrder'])->name('pagbank.smart-button.create-order');

        Route::post('/capture-order', [SmartButtonController::class, 'captureOrder'])->name('pagbank.smart-button.capture-order');
    });
});

Route::post('pagbank/standard/ipn', [StandardController::class, 'ipn'])
    ->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)
    ->name('pagbank.standard.ipn');
