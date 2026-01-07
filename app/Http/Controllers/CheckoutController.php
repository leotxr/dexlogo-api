<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\Webhook;
use App\Services\CheckoutService;
use App\Services\WebhookService;
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
    private $webhookService;

    public function __construct(CheckoutService $checkoutService, WebhookService $webhookService)
    {
        $this->checkoutService  = $checkoutService;
        $this->webhookService   = $webhookService;
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
        try {
            $checkout = $this->webhookService->processar($request);
            return $this->successResponse('Checkout iniciado com sucesso!', []);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), [], 400);
        }
    }
}
