<?php

namespace App\Services;

use App\Exceptions\MailerException;
use App\Mail\CodigoAcessoMail;
use App\Models\AccessCode;
use App\Models\Order;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
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
    private $paymentId;

    public function __construct()
    {
        MercadoPagoConfig::setAccessToken(config('services.mercadopago.access_token'));
        $this->paymentClient = new PaymentClient();
    }

    public function processarPagamento($paymentId)
    {
        // Validar pagamento usando o service
        $payment        = $this->buscarPagamento($paymentId);
        $orderExist     = Order::where('uuid', $payment->external_reference)->first();

        if (!$orderExist) {
            throw new Exception("Pedido não encontrado.");
        }

        $this->validarStatusPagamento($payment, $orderExist, $paymentId);

        return true;
    }

    private function buscarPagamento($id)
    {
        if (!$payment = $this->paymentClient->get($id)) {
            throw new Exception("Ocorreu um erro ao buscar o pagamento.");
        }

        return $payment;
    }

    public function gerarCodigo($orderId)
    {
        return AccessCode::create([
            'code'          => Str::upper(Str::random(8)),
            'expires_at'    => now()->addYear(),
            'order_id'      => $orderId
        ]);
    }

    private function validarStatusPagamento($payment, $order, $paymentId)
    {
        switch ($payment->status) {
            case 'approved':
                $this->aprovarPedido($order, $payment);
                break;

            case 'pending':
            case 'in_process':
                $order->update([
                    'payment_id'            => $paymentId,
                    'status'                => 'aguardando_pagamento',
                    'mp_payment_status'     => $payment->status,
                    'payment_status_detail' => $payment->status_detail,
                ]);
                break;

            case 'rejected':
                $order->update([
                    'payment_id'            => $paymentId,
                    'status'                => 'pagamento_recusado',
                    'payment_status'        => $payment->status,
                    'payment_status_detail' => $payment->status_detail,
                ]);
                //$this->notificarPagamentoRecusado($order, $payment->status_detail);
                break;

            case 'cancelled':
                $order->update([
                    'payment_id'    => $paymentId,
                    'status'        => 'cancelado',
                ]);
                break;

            case 'refunded':
                //$this->reverterPedido($order);
                break;

            default:
                Log::warning("Status desconhecido: {$payment->status}");
        }
    }

    private function aprovarPedido($order, $payment)
    {
        if ($order->accessCode) {
            Log::info("Pedido {$order->id} já possui código");
            return;
        }

        // Gerar código
        $accessCode = $this->gerarCodigo($order->id);

        if (!$accessCode) {
            throw new Exception('Ocorreu um erro ao gerar o codigo de acesso.');
        }

        // Atualizar pedido
        $order->update([
            'payment_id'        => $payment->id,
            'status'            => 'pago',
            'payment_method'    => $payment->payment_method_id,
            'payment_status'    => 'approved',
            'data_pagamento'    => $payment->date_approved ?? now(),
        ]);

        // Enviar logica de email pro job
        $this->enviarEmail($order, $accessCode);
        Log::info("Código gerado para pedido {$order->id}: {$accessCode}");
    }

    public function enviarEmail($order, $accessCode)
    {
        if(!Mail::to($order->email)->send(new CodigoAcessoMail($accessCode->code))) {
            throw new MailerException('Ocorreu um erro ao enviar o e-mail.', 400);
        }
    }
}
