<?php

namespace App\Traits;

trait ResponseTrait
{
    public function successResponse(string $message, array $data)
    {
        return response()->json([
            'erro'      =>  false,
            'mensagem'  => $message,
            'dados'     => $data
        ], 200);
    }

    public function errorResponse(string $message, array $errors, int $status = 400)
    {
        return response()->json([
            'erro'      =>  true,
            'mensagem'  => $message,
            'dados'     => $errors
        ], $status);
    }
}
