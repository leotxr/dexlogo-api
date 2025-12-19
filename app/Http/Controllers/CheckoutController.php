<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\Webhook;
use App\Services\CheckoutService;
use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Http\Request;
use MercadoPago\SDK;
use MercadoPago\Preference;
use MercadoPago\Item;
use MercadoPago\Payment;

class CheckoutController extends Controller
{
    use ResponseTrait;
    private $checkoutService;

    public function __construct(CheckoutService $checkoutService)
    {
        $this->checkoutService = $checkoutService;
    }

    public function start(CheckoutRequest $request)
    {
        $validated = $request->validated();

        try {
            $checkout = $this->checkoutService->criarPagamento($validated);
            return $this->successResponse('Checkout iniciado com sucesso!', $checkout);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), [], 400);
        }
    }

    public function webhook(Request $request)
    {
    }
}
