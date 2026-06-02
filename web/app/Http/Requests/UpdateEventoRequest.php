<?php

namespace App\Http\Requests;

use App\Models\Evento;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEventoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'titulo' => ['sometimes', 'required', 'string', 'max:255'],
            'descricao' => ['sometimes', 'nullable', 'string'],
            'data' => ['sometimes', 'required', 'date'],
            'horario' => ['sometimes', 'required', 'date_format:H:i'],
            'local' => ['sometimes', 'required', 'string', 'max:255'],
            'quantidade_vagas' => ['sometimes', 'required', 'integer', 'min:1'],
            'status' => ['sometimes', 'required', Rule::in([
                Evento::STATUS_ABERTO,
                Evento::STATUS_ENCERRADO,
                Evento::STATUS_CANCELADO,
            ])],
        ];
    }
}
