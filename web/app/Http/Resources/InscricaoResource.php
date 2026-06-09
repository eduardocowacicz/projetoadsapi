<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InscricaoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'evento_id' => $this->evento_id,
            'evento' => $this->whenLoaded('evento', fn() => [
                'id' => $this->evento->id,
                'titulo' => $this->evento->titulo,
            ]),
            'participante_id' => $this->participante_id,
            'participante' => $this->whenLoaded('participante', fn() => [
                'id' => $this->participante->id,
                'nome' => $this->participante->nome,
                'email' => $this->participante->email,
            ]),
            'status' => $this->status,
            'data_inscricao' => $this->data_inscricao?->toISOString(),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
