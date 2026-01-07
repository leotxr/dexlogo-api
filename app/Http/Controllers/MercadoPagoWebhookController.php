<?php

namespace App\Http\Controllers;

use App\Services\WebhookService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MercadoPagoWebhookController extends Controller
{
    use ResponseTrait;
    protected $webhookService;

    public function __construct(WebhookService $webhookService)
    {
        $this->webhookService = $webhookService;
    }

    public function handle(Request $request)
    {
        try {
            Log::info('Webhook Mercado Pago recebido:', $request->all());

            $type = $request->input('type');
            $dataId = $request->input('data.id');

            if ($type === 'payment') {
                $this->webhookService->processarPagamento($dataId);
            }

            return $this->successResponse('Webhook iniciado com sucesso!', []);
        } catch (\Exception $e) {
            Log::error('Erro no webhook Mercado Pago: ' . $e->getMessage());
            return response()->json(['status' => 'error'], 200);
        }
    }
}
