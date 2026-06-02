<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreParticipanteRequest;
use App\Http\Requests\UpdateParticipanteRequest;
use App\Http\Resources\ParticipanteResource;
use App\Services\ParticipanteService;
use Illuminate\Http\Request;

class ParticipanteController extends Controller
{
    public function __construct(private readonly ParticipanteService $participantes)
    {
    }

    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 15);

        return ParticipanteResource::collection($this->participantes->listar($perPage));
    }

    public function show(int $id): ParticipanteResource
    {
        return new ParticipanteResource($this->participantes->buscar($id));
    }

    public function store(StoreParticipanteRequest $request)
    {
        $participante = $this->participantes->criar($request->validated());

        return (new ParticipanteResource($participante))
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateParticipanteRequest $request, int $id): ParticipanteResource
    {
        $participante = $this->participantes->atualizar($id, $request->validated());

        return new ParticipanteResource($participante);
    }

    public function destroy(int $id)
    {
        $this->participantes->excluir($id);

        return response()->json(null, 204);
    }
}
