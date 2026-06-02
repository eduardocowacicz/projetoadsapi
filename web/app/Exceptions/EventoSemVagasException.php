<?php

namespace App\Exceptions;

class EventoSemVagasException extends ApiException
{
    public function __construct()
    {
        parent::__construct('Evento sem vagas disponiveis.', 422);
    }
}
