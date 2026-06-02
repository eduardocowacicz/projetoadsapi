<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'titulo' => ['required', 'string', 'max:255'],
            'descricao' => ['nullable', 'string'],
            'data' => ['required', 'date'],
            'horario' => ['required', 'date_format:H:i'],
            'local' => ['required', 'string', 'max:255'],
            'quantidade_vagas' => ['required', 'integer', 'min:1'],
        ];
    }
}
