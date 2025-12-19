<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Checkout extends Model
{
    use HasUuids, Notifiable;

    protected $fillable = [
        'nome',
        'email',
        'telefone',
        'plano',
        'valor',
        'status',
        'codigo_acesso',
        'mercadopago_payment_id',
        'mercadopago_preference_id',
        'paid_at'
    ];

    public function routeNotificationForMail()
    {
        return $this->email;
    }
}
