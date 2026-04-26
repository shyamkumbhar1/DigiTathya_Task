<?php

namespace App\Traits;

trait ApiResponseTrait
{
    protected function formatResponse(
        bool $success,
        string $message,
        $data = null,
        $errors = null
    ): array {
        return [
            'success' => $success,
            'message' => $message,
            'data' => $data,
            'errors' => $errors,
        ];
    }

    protected function apiResponse(
        bool $success,
        string $message,
        $data = null,
        $errors = null,
        int $status = 200
    ) {
        return response()->json(
            $this->formatResponse($success, $message, $data, $errors),
            $status
        );
    }
}
