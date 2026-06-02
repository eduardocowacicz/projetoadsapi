<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInscricaoRequest;
use App\Http\Resources\InscricaoResource;
use App\Services\InscricaoService;

class InscricaoController extends Controller
{
    public function __construct(private readonly InscricaoService $inscricoes)
    {
    }

    public function store(StoreInscricaoRequest $request)
    {
        $inscricao = $this->inscricoes->criar($request->validated());

        return (new InscricaoResource($inscricao))
            ->response()
            ->setStatusCode(201);
    }

    public function destroy(int $id): InscricaoResource
    {
        $inscricao = $this->inscricoes->cancelar($id);

        return new InscricaoResource($inscricao);
    }
}
