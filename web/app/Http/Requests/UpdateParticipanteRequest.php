<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateParticipanteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $participanteId = $this->route('id');

        return [
            'nome' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => [
                'sometimes',
                'required',
                'email',
                'max:255',
                Rule::unique('participantes', 'email')->ignore($participanteId),
            ],
            'telefone' => ['sometimes', 'nullable', 'string', 'max:20'],
        ];
    }
}
