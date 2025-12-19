<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccessCode extends Model
{
    protected $fillable = [
        'code',
        'order_id',
        'expires_at',
        'active'
    ];

    public function order()
    {
        $this->belongsTo(Order::class, 'order_id', 'id');
    }
}
