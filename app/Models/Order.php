<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'uuid',
        'nome',
        'email',
        'telefone',
        'plano',
        'valor',
        'payment_id',
        'preference_id',
        'external_reference',
        'status',
        'detalhes',
        'codigo_acesso'
    ];

    protected $casts = [
        'valor' => 'decimal:2',
    ];

    //temporaria
    public static function buscarValorPlano($planType)
    {
        $plans = [
            'basico' => 9.90,
            'pro' => 17.90,
        ];

        return $plans[$planType] ?? 0;
    }

    public function accessCode()
    {
        return $this->hasOne(AccessCode::class);
    }
}
