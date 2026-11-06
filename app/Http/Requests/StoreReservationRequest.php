<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreReservationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * Salas has no login and no admin role, so every visitor may reserve a room.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Trim the subject so the bounds below apply to the stored text.
     */
    protected function prepareForValidation(): void
    {
        if (is_string($this->input('subject'))) {
            $this->merge(['subject' => trim($this->string('subject')->value())]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * `room_id` is resolved by the controller, so an unknown room answers 404 instead of 422.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'date' => ['required', 'date_format:Y-m-d'],
            'start' => ['required', 'date_format:H:i'],
            'end' => ['required', 'date_format:H:i'],
            'subject' => ['required', 'string', 'max:200'],
        ];
    }

    /**
     * Get the messages the visitor reads for each failing rule.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'date.required' => 'Informe data e horários válidos',
            'date.date_format' => 'Informe data e horários válidos',
            'start.required' => 'Informe data e horários válidos',
            'start.date_format' => 'Informe data e horários válidos',
            'end.required' => 'Informe data e horários válidos',
            'end.date_format' => 'Informe data e horários válidos',
            'subject.required' => 'Informe o assunto',
            'subject.string' => 'Informe o assunto',
            'subject.max' => 'O assunto deve ter no máximo 200 caracteres',
        ];
    }
}
