<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Webhook extends Model
{
        protected $fillable = [
        'order_id',
        'resposta'
    ];


    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
