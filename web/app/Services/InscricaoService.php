<?php

namespace App\Services;

use App\Exceptions\EntidadeNaoEncontradaException;
use App\Exceptions\EventoCanceladoException;
use App\Exceptions\EventoEncerradoException;
use App\Exceptions\EventoSemVagasException;
use App\Exceptions\InscricaoDuplicadaException;
use App\Interfaces\EventoRepositoryInterface;
use App\Interfaces\InscricaoRepositoryInterface;
use App\Interfaces\ParticipanteRepositoryInterface;
use App\Models\Evento;
use App\Models\Inscricao;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class InscricaoService
{
    public function __construct(
        private readonly EventoRepositoryInterface $eventos,
        private readonly ParticipanteRepositoryInterface $participantes,
        private readonly InscricaoRepositoryInterface $inscricoes,
    ) {}

    public function listar(int $perPage): LengthAwarePaginator
    {
        return $this->inscricoes->paginate($perPage);
    }

    public function criar(array $data): Inscricao
    {
        return DB::transaction(function () use ($data): Inscricao {
            $evento = $this->eventos->findByIdForUpdate($data['evento_id']);

            if (! $evento) {
                throw new EntidadeNaoEncontradaException('Evento');
            }

            $participante = $this->participantes->findById($data['participante_id']);

            if (! $participante) {
                throw new EntidadeNaoEncontradaException('Participante');
            }

            if ($evento->status === Evento::STATUS_CANCELADO) {
                throw new EventoCanceladoException();
            }

            if ($evento->status === Evento::STATUS_ENCERRADO) {
                throw new EventoEncerradoException();
            }

            $existente = $this->inscricoes
                ->findByEventoAndParticipante($evento->id, $participante->id);

            if ($existente) {
                throw new InscricaoDuplicadaException();
            }

            if ($evento->vagas_disponiveis <= 0) {
                throw new EventoSemVagasException();
            }

            $inscricao = $this->inscricoes->create([
                'evento_id' => $evento->id,
                'participante_id' => $participante->id,
                'status' => Inscricao::STATUS_ATIVA,
                'data_inscricao' => now(),
            ]);

            $this->eventos->update($evento, [
                'vagas_disponiveis' => $evento->vagas_disponiveis - 1,
            ]);

            return $inscricao;
        });
    }

    public function cancelar(int $id): Inscricao
    {
        return DB::transaction(function () use ($id): Inscricao {
            $inscricao = $this->inscricoes->findById($id);

            if (! $inscricao) {
                throw new EntidadeNaoEncontradaException('Inscricao');
            }

            if ($inscricao->status === Inscricao::STATUS_CANCELADA) {
                return $inscricao;
            }

            $evento = $this->eventos->findByIdForUpdate($inscricao->evento_id);

            if (! $evento) {
                throw new EntidadeNaoEncontradaException('Evento');
            }

            $this->inscricoes->update($inscricao, [
                'status' => Inscricao::STATUS_CANCELADA,
            ]);

            $novaDisponibilidade = min(
                $evento->quantidade_vagas,
                $evento->vagas_disponiveis + 1
            );

            $this->eventos->update($evento, [
                'vagas_disponiveis' => $novaDisponibilidade,
            ]);

            return $inscricao->refresh();
        });
    }
}
