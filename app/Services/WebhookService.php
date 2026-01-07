<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class WebhookService extends Service 
{
    public function __construct()
    {
        return parent::__construct();
    }

    public function processar($request)
    {
        Log::info('Webhook recebido', [
        'headers' => $request->headers->all(),
        'body'    => $request->all(),
        'raw'     => $request->getContent(),
    ]);

    return response()->json(['ok' => true]);
    }
}

