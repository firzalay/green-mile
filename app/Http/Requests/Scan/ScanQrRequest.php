<?php

namespace App\Http\Requests\Scan;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ScanQrRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'qr_token' => ['required', 'string'],
        ];
    }

    /**
     * Get custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'qr_token.required' => 'QR Code tidak valid.',
            'qr_token.string' => 'QR Code tidak valid.',
        ];
    }

    /**
     * Handle a failed validation attempt by returning a JSON response.
     */
    protected function failedValidation(Validator $validator): never
    {
        throw new HttpResponseException(
            response()->json([
                'status' => 'error',
                'message' => 'QR Code tidak valid.',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
