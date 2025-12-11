<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMedicalRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'appointment_id' => [
                'required',
                'integer',
                Rule::exists('appointments', 'id'),
                Rule::unique('medical_records', 'appointment_id')
            ],
            'diagnosis' => ['required', 'string'],
            'treatment' => ['required', 'string'],
            'prescriptions' => ['nullable', 'string'],
            'notes' => ['nullable', 'string', 'max:500']
        ];
    }
}
