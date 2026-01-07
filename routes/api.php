<?php

use App\Http\Controllers\CheckoutController;
use App\Models\Checkout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/teste', function () {
    return response()->json('Teste API DexLogo.');
})->name('teste');

Route::prefix('checkout')->group(function () {
    Route::post('/iniciar', [CheckoutController::class, 'start'])->name('checkout.iniciar');
    Route::get('/sucesso/{id}', [CheckoutController::class, 'success'])->name('checkout.successo');
    Route::post('/webhook', [CheckoutController::class, 'webhook'])->name('checkout.webhook');
});