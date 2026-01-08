<?php

namespace App\Services;

use App\Models\AccessCode;
use App\Models\Order;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Resources\Order\Payment as OrderPayment;
use MercadoPago\Resources\Payment;
use MercadoPago\Resources\PaymentSearch;

class WebhookService extends Service
{
    private $paymentClient;
    private $payment;

    public function __construct()
    {
        MercadoPagoConfig::setAccessToken(config('services.mercadopago.access_token'));
        $this->paymentClient = new PaymentClient();
    }

    public function processarPagamento($id)
    {
        // Validar pagamento usando o service
        $validacao = $this->validarPagamento($id);

        if (!$validacao['aprovado']) {
            throw new Exception("Pagamento {$id} não foi aprovado. Status: " . ($validacao['status'] ?? 'desconhecido'));
        }
        
        $pedidoExistente = Order::where('uuid', $validacao['external_reference'])->first();

        if (!$pedidoExistente) {
            throw new Exception("Pedido não encontrado.");
        }

        // Gerar código para o cliente
        $this->gerarCodigo($pedidoExistente->id);
    }

    private function buscarPagamento($id)
    {
        if (!$payment = $this->paymentClient->get($id)) {
            throw new Exception("Ocorreu um erro ao buscar o pagamento.");
        }

        return $payment;
    }

    private function validarPagamento($id)
    {
        $payment = $this->buscarPagamento($id);

        return [
            'aprovado' => $payment->status === 'approved',
            'status' => $payment->status,
            'valor' => $payment->transaction_amount,
            'email' => $payment->payer->email,
            'external_reference' => $payment->external_reference,
            'payment_method' => $payment->payment_method_id,
            'payment_type' => $payment->payment_type_id,
            'data_aprovacao' => $payment->date_approved
        ];
    }

    public function gerarCodigo($orderId)
    {
        return AccessCode::create([
            'code'          => Str::upper(Str::random(16)),
            'expires_at'    => now()->addYear(),
            'order_id'      => $orderId
        ]);
    }
}
