<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAppointmentRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'appointment_date' => ['required', 'date', 'after:now', 'date_format:Y-m-d H:i:s'],

            'doctor_id' => [
                'nullable',
                'integer',
                Rule::exists('doctors', 'id'),
                'prohibits:nurse_id'
            ],
            'nurse_id' => [
                'nullable',
                'integer',
                Rule::exists('nurses', 'id'),
                'prohibits:doctor_id'
            ],

            'type' => ['required', 'string', Rule::in(['presencial', 'telemedicina'])],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'appointment_date.after' => 'A data do agendamento deve ser uma data futura.',
            'doctor_id.prohibits' => 'O agendamento não pode ter um médico e um enfermeiro simultaneamente.',
            'nurse_id.prohibits' => 'O agendamento não pode ter um médico e um enfermeiro simultaneamente.',
            'type.in' => 'O tipo de consulta deve ser "presencial" ou "telemedicina".',
            'notes.max' => 'As notas não podem exceder 500 caracteres.',
            'appointment_date.date_format' => 'O formato da data do agendamento deve ser Y-m-d H:i:s.'
        ];
    }
}
