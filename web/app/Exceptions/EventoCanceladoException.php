<?php

namespace App\Exceptions;

class EventoCanceladoException extends ApiException
{
    public function __construct()
    {
        parent::__construct('Evento cancelado.', 422);
    }
}
