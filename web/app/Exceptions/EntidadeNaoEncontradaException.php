<?php

namespace App\Exceptions;

class EntidadeNaoEncontradaException extends ApiException
{
    public function __construct(string $entidade)
    {
        parent::__construct(sprintf('%s nao encontrado(a).', $entidade), 404);
    }
}
