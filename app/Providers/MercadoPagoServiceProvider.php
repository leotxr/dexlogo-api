<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use MercadoPago\SDK;

class MercadoPagoServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('mercadopago', function () {
            SDK::setAccessToken(config('services.mercadopago.access_token'));
            return new SDK();
        });
    }

    public function boot()
    {
        //
    }
}