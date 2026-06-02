<?php

namespace App\Exceptions;

class EventoEncerradoException extends ApiException
{
    public function __construct()
    {
        parent::__construct('Evento encerrado.', 422);
    }
}
