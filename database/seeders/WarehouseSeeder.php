<?php

namespace Database\Seeders;

use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    /**
     * Depósitos. El producto referencia su depósito activo guardando el
     * código en products.warehouse.
     */
    private const WAREHOUSES = [
        ['codigo' => 'GENERAL', 'nombre' => 'Depósito General'],
    ];

    public function run(): void
    {
        foreach (self::WAREHOUSES as $warehouse) {
            Warehouse::updateOrCreate(
                ['codigo' => $warehouse['codigo']],
                $warehouse
            );
        }
    }
}
