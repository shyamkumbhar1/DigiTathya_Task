<?php

namespace App\Http\Requests;

use App\Traits\ApiResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ScanIngestRequest extends FormRequest
{
    use ApiResponseTrait;

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
            'action' => ['required', 'in:receive,dispatch,verify'],
            'gps_lat' => ['nullable', 'numeric', 'between:-90,90'],
            'gps_lng' => ['nullable', 'numeric', 'between:-180,180'],
            'device_timestamp' => ['nullable', 'date'],
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json(
            $this->formatResponse(
                false,
                'Validation failed',
                null,
                [
                    'code' => 'VALIDATION_ERROR',
                    'details' => $validator->errors(),
                ]
            ),
            422
        ));
    }
}
