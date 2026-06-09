<?php

namespace App\Exceptions;

class EventoSemPermissaoException extends ApiException
{
    public function __construct()
    {
        parent::__construct('Voce nao tem permissao para alterar este evento.', 403);
    }
}
