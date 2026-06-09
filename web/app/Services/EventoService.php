<?php

namespace App\Services;

use App\Exceptions\EntidadeNaoEncontradaException;
use App\Exceptions\EventoSemPermissaoException;
use App\Interfaces\EventoRepositoryInterface;
use App\Interfaces\InscricaoRepositoryInterface;
use App\Models\Evento;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class EventoService
{
    public function __construct(
        private readonly EventoRepositoryInterface $eventos,
        private readonly InscricaoRepositoryInterface $inscricoes,
    ) {}

    public function listar(int $perPage): LengthAwarePaginator
    {
        return $this->eventos->paginate($perPage);
    }

    public function buscar(int $id): Evento
    {
        $evento = $this->eventos->findById($id);

        if (! $evento) {
            throw new EntidadeNaoEncontradaException('Evento');
        }

        return $evento;
    }

    public function criar(array $data, int $userId): Evento
    {
        $data['user_id'] = $userId;
        $data['status'] = Evento::STATUS_ABERTO;
        $data['vagas_disponiveis'] = $data['quantidade_vagas'];

        return $this->eventos->create($data);
    }

    public function atualizar(int $id, array $data, int $userId): Evento
    {
        return DB::transaction(function () use ($id, $data, $userId): Evento {
            $evento = $this->buscar($id);

            $this->validarDono($evento, $userId);

            if (array_key_exists('quantidade_vagas', $data)) {
                $usadas = $evento->quantidade_vagas - $evento->vagas_disponiveis;
                $novaQuantidade = (int) $data['quantidade_vagas'];
                $data['vagas_disponiveis'] = max(0, $novaQuantidade - $usadas);
            }

            $novoStatus = $data['status'] ?? null;
            $statusFinaliza = in_array($novoStatus, [Evento::STATUS_CANCELADO, Evento::STATUS_ENCERRADO], true);
            $statusMudou = $novoStatus !== null && $novoStatus !== $evento->status;

            if ($statusFinaliza && $statusMudou) {
                $this->inscricoes->cancelarAtivas($evento->id);
                $data['vagas_disponiveis'] = $data['quantidade_vagas'] ?? $evento->quantidade_vagas;
            }

            return $this->eventos->update($evento, $data);
        });
    }

    public function excluir(int $id, int $userId): void
    {
        $evento = $this->buscar($id);

        $this->validarDono($evento, $userId);

        $this->eventos->delete($evento);
    }

    public function listarComVagasDisponiveis(): Collection
    {
        return $this->eventos->listAvailable();
    }

    public function listarParticipantes(int $id): Collection
    {
        $evento = $this->buscar($id);

        return $this->eventos->getActiveParticipants($evento);
    }

    private function validarDono(Evento $evento, int $userId): void
    {
        if ((int) $evento->user_id !== $userId) {
            throw new EventoSemPermissaoException();
        }
    }
}
