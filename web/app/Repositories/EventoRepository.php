<?php

namespace App\Repositories;

use App\Interfaces\EventoRepositoryInterface;
use App\Models\Evento;
use App\Models\Inscricao;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class EventoRepository implements EventoRepositoryInterface
{
    public function paginate(int $perPage): LengthAwarePaginator
    {
        return Evento::query()
            ->orderBy('data')
            ->orderBy('horario')
            ->paginate($perPage);
    }

    public function findById(int $id): ?Evento
    {
        return Evento::query()->find($id);
    }

    public function findByIdForUpdate(int $id): ?Evento
    {
        return Evento::query()->whereKey($id)->lockForUpdate()->first();
    }

    public function create(array $data): Evento
    {
        return Evento::query()->create($data);
    }

    public function update(Evento $evento, array $data): Evento
    {
        $evento->fill($data);
        $evento->save();

        return $evento;
    }

    public function delete(Evento $evento): void
    {
        $evento->delete();
    }

    public function listAvailable(): Collection
    {
        return Evento::query()
            ->where('status', Evento::STATUS_ABERTO)
            ->where('vagas_disponiveis', '>', 0)
            ->orderBy('data')
            ->orderBy('horario')
            ->get();
    }

    public function getActiveParticipants(Evento $evento): Collection
    {
        return $evento->participantes()
            ->wherePivot('status', Inscricao::STATUS_ATIVA)
            ->get();
    }
}
