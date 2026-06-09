<?php

namespace App\Interfaces;

use App\Models\Inscricao;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface InscricaoRepositoryInterface
{
    public function paginate(int $perPage): LengthAwarePaginator;

    public function findById(int $id): ?Inscricao;

    public function findByEventoAndParticipante(int $eventoId, int $participanteId): ?Inscricao;

    public function create(array $data): Inscricao;

    public function update(Inscricao $inscricao, array $data): Inscricao;

    public function cancelarAtivas(int $eventoId): void;
}
