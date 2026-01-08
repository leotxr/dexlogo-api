<?php

namespace App\Services;

use App\Models\AccessCode;
use App\Models\Order;
use Exception;
use Illuminate\Support\Str;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Preference;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Resources\Preference as ResourcesPreference;
use MercadoPago\Resources\Preference\Item;
use MercadoPago\Resources\Preference\Payer;

class CheckoutService extends Service
{
    private $preference;
    private $pagamento;

    public function __construct()
    {
        MercadoPagoConfig::setAccessToken(config('services.mercadopago.access_token'));
    }

    public function criarPagamento($data)
    {
        $this->criarOrder($data);
        $this->criarPreferencia($data);

        // Atualizar o pedido
        $this->pagamento->update([
            'preference_id' => $this->preference->id
        ]);

        return [
            'sucesso'       => true,
            'order_id'      => $this->pagamento->id,
            'uuid'          => $this->pagamento->uuid,
            'url_pagamento' => $this->preference->init_point,
            'mensagem'      => 'Pedido criado com sucesso!'
        ];
    }

    private function criarOrder($data)
    {
        $this->pagamento = Order::create([
            'uuid'      =>  (string) Str::orderedUuid(),
            'nome'      =>  $data['nome'],
            'email'     =>  $data['email'],
            'telefone'  =>  $data['telefone'],
            'plano'     =>  $data['plano'],
            'status'    =>  'pendente',
            'valor'     =>  $this->checkPlanValue($data['plano'])
        ]);

        if (!$this->pagamento) {
            throw new Exception('Não foi possível iniciar o pagamento.');
        }
    }

    private function criarPreferencia($data)
    {
        $client = new PreferenceClient();

        $this->preference = $client->create([
            'items'     =>  [
                $this->gerarItem($data)
            ],
            'payer'     =>  [
                $this->gerarPayer($data)
            ],
            'back_urls' => [
                'success' => url('/payment/success?order_id=' . $this->pagamento->uuid),
                'failure' => url('/payment/failure?order_id=' . $this->pagamento->uuid),
                'pending' => url('/payment/pending?order_id=' . $this->pagamento->uuid)
            ],
            'external_reference' => (string) $this->pagamento->uuid,
            'notification_url' => url('/api/checkout/webhook/' . $this->pagamento->uuid),
        ]);

        if (!$this->preference) {
            throw new Exception('Ocorreu um erro ao gerar a Preferencia de pagamento');
        }
    }

    private function gerarItem($data)
    {
        $item = [];
        $item['title']          = 'Plano ' . ucfirst($data['plano']);
        $item['description']    = 'Criação de logo Profissional Plano ' . ucfirst($data['plano']);
        $item['quantity']       = 1;
        $item['unit_price']     = (float) $this->checkPlanValue($data['plano']);
        $item['currency_id']    =  'BRL';

        return $item;
    }

    private function gerarPayer($data)
    {
        $payer = [];

        $payer['name']      = $data['nome'];
        $payer['email']     = $data['email'];
        $payer['phone']     = [
            "area_code" => "",
            "number"    => $data['telefone']
        ];

        return $payer;
    }

    // Alterar para salvar no banco estes valores e recuperar, ou passar direto no json
    private function checkPlanValue($plan)
    {
        return match ($plan) {
            'pro'       =>  17.90,
            'basico'    =>  9.90
        };
    }
}
