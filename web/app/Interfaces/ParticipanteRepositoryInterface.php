<?php

namespace App\Interfaces;

use App\Models\Participante;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ParticipanteRepositoryInterface
{
    public function paginate(int $perPage): LengthAwarePaginator;

    public function findById(int $id): ?Participante;

    public function create(array $data): Participante;

    public function update(Participante $participante, array $data): Participante;

    public function delete(Participante $participante): void;
}
