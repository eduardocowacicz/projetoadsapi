<?php

namespace App\Services;

use App\Exceptions\EntidadeNaoEncontradaException;
use App\Interfaces\ParticipanteRepositoryInterface;
use App\Models\Participante;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ParticipanteService
{
    public function __construct(private readonly ParticipanteRepositoryInterface $participantes)
    {
    }

    public function listar(int $perPage): LengthAwarePaginator
    {
        return $this->participantes->paginate($perPage);
    }

    public function buscar(int $id): Participante
    {
        $participante = $this->participantes->findById($id);

        if (! $participante) {
            throw new EntidadeNaoEncontradaException('Participante');
        }

        return $participante;
    }

    public function criar(array $data): Participante
    {
        return $this->participantes->create($data);
    }

    public function atualizar(int $id, array $data): Participante
    {
        $participante = $this->buscar($id);

        return $this->participantes->update($participante, $data);
    }

    public function excluir(int $id): void
    {
        $participante = $this->buscar($id);
        $this->participantes->delete($participante);
    }
}
