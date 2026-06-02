<?php

namespace App\Exceptions;

class InscricaoDuplicadaException extends ApiException
{
    public function __construct()
    {
        parent::__construct('Participante ja inscrito neste evento.', 409);
    }
}
