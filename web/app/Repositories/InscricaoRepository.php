<?php

namespace App\Repositories;

use App\Interfaces\InscricaoRepositoryInterface;
use App\Models\Inscricao;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class InscricaoRepository implements InscricaoRepositoryInterface
{
    public function paginate(int $perPage): LengthAwarePaginator
    {
        return Inscricao::with(['evento', 'participante'])
            ->orderByDesc('data_inscricao')
            ->paginate($perPage);
    }

    public function findById(int $id): ?Inscricao
    {
        return Inscricao::query()->find($id);
    }

    public function findByEventoAndParticipante(int $eventoId, int $participanteId): ?Inscricao
    {
        return Inscricao::query()
            ->where('evento_id', $eventoId)
            ->where('participante_id', $participanteId)
            ->where('status', Inscricao::STATUS_ATIVA)
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

    public function cancelarAtivas(int $eventoId): void
    {
        Inscricao::query()
            ->where('evento_id', $eventoId)
            ->where('status', Inscricao::STATUS_ATIVA)
            ->update(['status' => Inscricao::STATUS_CANCELADA]);
    }
}
