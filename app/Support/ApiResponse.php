<?php

namespace App\Support;

class ApiResponse
{
    public static function success(string $message, $data = null): array
    {
        return [
            'success' => true,
            'message' => $message,
            'data' => $data,
            'errors' => null,
        ];
    }

    public static function error(string $message, string $code, $details = null): array
    {
        return [
            'success' => false,
            'message' => $message,
            'data' => null,
            'errors' => [
                'code' => $code,
                'details' => $details,
            ],
        ];
    }
}
