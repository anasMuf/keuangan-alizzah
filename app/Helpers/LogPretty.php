<?php
namespace App\Helpers;

use Illuminate\Support\Facades\Log;

class LogPretty
{
    public static function error($e,$request = []){
        $e = is_array($e) ? $e : [
            'message' => 'Unknown error',
            'file' => 'Unknown file',
            'line' => 0,
        ];
        if($e instanceof \Exception) {
            $e = [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ];
        }
        Log::error(json_encode([
            'message' => $e['message'],
            'file' => $e['file'],
            'line' => $e['line'],
            'request' => $request,
        ],JSON_PRETTY_PRINT));
    }

    public static function info($message, $data = []){
        Log::info(json_encode([
            'message' => $message,
            'data' => $data,
        ],JSON_PRETTY_PRINT));
    }
}
