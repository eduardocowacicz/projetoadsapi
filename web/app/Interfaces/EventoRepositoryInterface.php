<?php

namespace App\Interfaces;

use App\Models\Evento;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface EventoRepositoryInterface
{
    public function paginate(int $perPage): LengthAwarePaginator;

    public function findById(int $id): ?Evento;

    public function findByIdForUpdate(int $id): ?Evento;

    public function create(array $data): Evento;

    public function update(Evento $evento, array $data): Evento;

    public function delete(Evento $evento): void;

    public function listAvailable(): Collection;

    public function getActiveParticipants(Evento $evento): Collection;
}
