<?php

namespace App\Http\Requests;

use App\Support\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ScanIngestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'scan_id' => ['required', 'string', 'max:255'],
            'session_id' => ['nullable', 'string', 'max:255'],
            'operator_id' => ['nullable', 'string', 'max:255'],
            'partner_id' => ['nullable', 'string', 'max:255'],
            'device_id' => ['nullable', 'string', 'max:255'],
            'action' => ['required', 'in:receive,dispatch,verify','return'],
            'gps_lat' => ['nullable', 'numeric', 'between:-90,90'],
            'gps_lng' => ['nullable', 'numeric', 'between:-180,180'],
            'device_timestamp' => ['nullable', 'date'],
            'gps_accuracy' => ['nullable', 'numeric', 'min:0'],
            'app_version' => ['nullable', 'string', 'max:255'],

        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json(
            ApiResponse::error(
                'Validation failed',
                'VALIDATION_ERROR',
                $validator->errors()->toArray()
            ),
            422
        ));
    }
}
