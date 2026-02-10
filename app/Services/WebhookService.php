<?php

namespace App\Services;

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
        return $this->buscarPagamento($id);

        return [
            'aprovado'              => $payment->status === 'approved',
            'status'                => $payment->status,
            'valor'                 => $payment->transaction_amount,
            'email'                 => $payment->payer->email,
            'external_reference'    => $payment->external_reference,
            'payment_method'        => $payment->payment_method_id,
            'payment_type'          => $payment->payment_type_id,
            'data_aprovacao'        => $payment->date_approved
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
        $code = $this->gerarCodigo($order->id);

        if(!$code) {
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

        // Enviar email
        //Mail::to($order->email)->send(new CodigoGerado($pedido));

        Log::info("Código gerado para pedido {$order->id}: {$code}");
    }
}
