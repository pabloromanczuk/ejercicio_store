<?php

namespace App\Services;

use RuntimeException;

class StockInsuficienteException extends RuntimeException
{
    public function __construct(public readonly string $detalle, public readonly int $disponible, public readonly int $solicitado)
    {
        parent::__construct("Stock insuficiente para \"{$detalle}\": disponible {$disponible}, solicitado {$solicitado}.");
    }
}
