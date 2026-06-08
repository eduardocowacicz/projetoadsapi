<?php

namespace App\Interfaces;

use App\Models\Inscricao;

interface InscricaoRepositoryInterface
{
    public function findById(int $id): ?Inscricao;

    public function findByEventoAndParticipante(int $eventoId, int $participanteId): ?Inscricao;

    public function create(array $data): Inscricao;

    public function update(Inscricao $inscricao, array $data): Inscricao;

    public function cancelarAtivas(int $eventoId): void;
}
