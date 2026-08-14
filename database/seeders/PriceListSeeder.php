<?php

namespace Database\Seeders;

use App\Models\PriceList;
use Illuminate\Database\Seeder;

class PriceListSeeder extends Seeder
{
    private const PRICE_LISTS = [
        [
            'codigo' => 'LISTA-PUBLICA',
            'nombre' => 'Lista Pública',
            'version' => 'v1',
            'vigencia_desde' => '2026-01-01',
            'vigencia_hasta' => null,
        ],
        [
            'codigo' => 'LISTA-MAYORISTA',
            'nombre' => 'Lista Mayorista',
            'version' => 'v1',
            'vigencia_desde' => '2026-01-01',
            'vigencia_hasta' => null,
        ],
    ];

    public function run(): void
    {
        foreach (self::PRICE_LISTS as $lista) {
            PriceList::updateOrCreate(
                ['codigo' => $lista['codigo']],
                $lista
            );
        }
    }
}
