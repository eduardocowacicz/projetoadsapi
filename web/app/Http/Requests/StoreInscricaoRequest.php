<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInscricaoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'evento_id' => ['required', 'integer', 'exists:eventos,id'],
            'participante_id' => ['required', 'integer', 'exists:participantes,id'],
        ];
    }
}
