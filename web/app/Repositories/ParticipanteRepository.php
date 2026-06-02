<?php

namespace App\Repositories;

use App\Interfaces\ParticipanteRepositoryInterface;
use App\Models\Participante;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ParticipanteRepository implements ParticipanteRepositoryInterface
{
    public function paginate(int $perPage): LengthAwarePaginator
    {
        return Participante::query()
            ->orderBy('nome')
            ->paginate($perPage);
    }

    public function findById(int $id): ?Participante
    {
        return Participante::query()->find($id);
    }

    public function create(array $data): Participante
    {
        return Participante::query()->create($data);
    }

    public function update(Participante $participante, array $data): Participante
    {
        $participante->fill($data);
        $participante->save();

        return $participante;
    }

    public function delete(Participante $participante): void
    {
        $participante->delete();
    }
}
