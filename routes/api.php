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
Route::get('/test-mp', function() {
    try {
        MercadoPago\MercadoPagoConfig::setAccessToken(config('services.mercadopago.access_token'));
        
        $client = new MercadoPago\Client\Preference\PreferenceClient();
        
        $preference = $client->create([
            'items' => [
                [
                    'title' => 'Produto Teste',
                    'quantity' => 1,
                    'unit_price' => 100,
                    'currency_id' => 'BRL'
                ]
            ]
        ]);
        
        return response()->json([
            'success' => true,
            'preference_id' => $preference->id,
            'init_point' => $preference->init_point
        ]);
        
    } catch (\MercadoPago\Exceptions\MPApiException $e) {
        return response()->json([
            'error' => 'MPApiException',
            'message' => $e->getMessage(),
            'status_code' => $e->getStatusCode(),
            'api_response' => $e->getApiResponse()
        ], 500);
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Exception',
            'message' => $e->getMessage()
        ], 500);
    }
});