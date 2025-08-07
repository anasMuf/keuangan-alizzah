<?php
namespace App\Helpers;

use Illuminate\Support\Facades\Log;

class LogPretty
{
    public static function error($e, $request = [])
    {
        Log::error(json_encode([
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'request' => $request,
        ], JSON_PRETTY_PRINT));
    }

    public static function info($message, $data = []){
        Log::info(json_encode([
            'message' => $message,
            'data' => $data,
        ],JSON_PRETTY_PRINT));
    }
}
