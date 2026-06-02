<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEventoRequest;
use App\Http\Requests\UpdateEventoRequest;
use App\Http\Resources\EventoResource;
use App\Http\Resources\ParticipanteResource;
use App\Services\EventoService;
use Illuminate\Http\Request;

class EventoController extends Controller
{
    public function __construct(private readonly EventoService $eventos)
    {
    }

    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 15);

        return EventoResource::collection($this->eventos->listar($perPage));
    }

    public function show(int $id): EventoResource
    {
        return new EventoResource($this->eventos->buscar($id));
    }

    public function store(StoreEventoRequest $request)
    {
        $evento = $this->eventos->criar($request->validated());

        return (new EventoResource($evento))
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateEventoRequest $request, int $id): EventoResource
    {
        $evento = $this->eventos->atualizar($id, $request->validated());

        return new EventoResource($evento);
    }

    public function destroy(int $id)
    {
        $this->eventos->excluir($id);

        return response()->json(null, 204);
    }

    public function vagasDisponiveis()
    {
        return EventoResource::collection($this->eventos->listarComVagasDisponiveis());
    }

    public function participantes(int $id)
    {
        return ParticipanteResource::collection($this->eventos->listarParticipantes($id));
    }
}
