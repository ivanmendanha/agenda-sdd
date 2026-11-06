<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreRoomRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * Salas has no login and no admin role, so every visitor may create a room.
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
            'name' => ['required', 'string', 'max:100'],
            'capacity' => ['required', 'integer', 'between:1,999'],
            'resources' => ['nullable', 'array'],
            'resources.*' => ['nullable', 'string'],
            'active' => ['nullable', 'boolean'],
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
            'name.required' => 'Informe o nome da sala',
            'name.string' => 'Informe o nome da sala',
            'name.max' => 'O nome da sala deve ter no máximo 100 caracteres',
            'capacity.required' => 'A capacidade deve ser um número inteiro de 1 a 999',
            'capacity.integer' => 'A capacidade deve ser um número inteiro de 1 a 999',
            'capacity.between' => 'A capacidade deve ser um número inteiro de 1 a 999',
        ];
    }
}
