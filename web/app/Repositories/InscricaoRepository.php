<?php

namespace App\Repositories;

use App\Interfaces\InscricaoRepositoryInterface;
use App\Models\Inscricao;

class InscricaoRepository implements InscricaoRepositoryInterface
{
    public function findById(int $id): ?Inscricao
    {
        return Inscricao::query()->find($id);
    }

    public function findByEventoAndParticipante(int $eventoId, int $participanteId): ?Inscricao
    {
        return Inscricao::query()
            ->where('evento_id', $eventoId)
            ->where('participante_id', $participanteId)
            ->first();
    }

    public function create(array $data): Inscricao
    {
        return Inscricao::query()->create($data);
    }

    public function update(Inscricao $inscricao, array $data): Inscricao
    {
        $inscricao->fill($data);
        $inscricao->save();

        return $inscricao;
    }
}
