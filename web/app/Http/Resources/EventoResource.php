<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'titulo' => $this->titulo,
            'descricao' => $this->descricao,
            'data' => $this->data?->format('Y-m-d'),
            'horario' => $this->horario,
            'local' => $this->local,
            'quantidade_vagas' => $this->quantidade_vagas,
            'vagas_disponiveis' => $this->vagas_disponiveis,
            'status' => $this->status,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
