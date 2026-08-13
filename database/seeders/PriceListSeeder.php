<?php

namespace Database\Seeders;

use App\Models\PriceList;
use Illuminate\Database\Seeder;

class PriceListSeeder extends Seeder
{
    /**
     * Versiones de listas de precios. El producto referencia su lista activa
     * guardando el código en products.pricelist.
     */
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
