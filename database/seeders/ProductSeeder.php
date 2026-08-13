<?php

namespace Database\Seeders;

use App\Models\PriceListProduct;
use App\Models\Product;
use App\Models\WarehouseProduct;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Los datos de origen (planilla / sistema legacy) vienen con tipos
     * inconsistentes a propósito:
     *  - los precios a veces son string, o usan coma decimal ("147,10")
     *  - el stock a veces es string, o viene con ".0" ("31.0")
     * normalizamos todo acá antes de persistir, para que las tablas queden
     * siempre con tipos numéricos consistentes.
     *
     * El producto ya NO guarda precio ni stock: solo los códigos de su lista
     * de precios (products.pricelist) y su depósito (products.warehouse).
     * El precio/IVA viven en price_list_product (self::PRECIOS) y el stock
     * en warehouse_product (self::STOCK).
     */
    private const PRODUCTOS = [
        ['codigo' => 324, 'detalle' => 'Manzanas', 'pricelist' => 'LISTA-PUBLICA', 'warehouse' => 'GENERAL'],
        ['codigo' => 637, 'detalle' => 'Naranjas', 'pricelist' => 'LISTA-PUBLICA', 'warehouse' => 'GENERAL'],
        ['codigo' => 711, 'detalle' => 'Peras', 'pricelist' => 'LISTA-MAYORISTA', 'warehouse' => 'GENERAL'],
    ];

    /**
     * Asignación lista-producto: precio e IVA que aplican para cada producto
     * según la lista que tiene asignada.
     */
    private const PRECIOS = [
        ['codigo' => 324, 'lista' => 'LISTA-PUBLICA', 'precio_unitario' => 500.25, 'iva' => 21],
        ['codigo' => 637, 'lista' => 'LISTA-PUBLICA', 'precio_unitario' => '334.336', 'iva' => 13],
        ['codigo' => 711, 'lista' => 'LISTA-MAYORISTA', 'precio_unitario' => '147,10', 'iva' => 21],
    ];

    /**
     * Asignación depósito-producto: cantidad de stock de cada producto en su
     * depósito.
     */
    private const STOCK = [
        ['codigo' => 324, 'deposito' => 'GENERAL', 'stock' => 45],
        ['codigo' => 637, 'deposito' => 'GENERAL', 'stock' => '14'],
        ['codigo' => 711, 'deposito' => 'GENERAL', 'stock' => '31.0'],
    ];

    public function run(): void
    {
        $listasPorCodigo = \App\Models\PriceList::pluck('id', 'codigo');
        $depositosPorCodigo = \App\Models\Warehouse::pluck('id', 'codigo');

        foreach (self::PRODUCTOS as $producto) {
            Product::updateOrCreate(
                ['codigo' => $producto['codigo']],
                [
                    'detalle' => $producto['detalle'],
                    'pricelist' => $producto['pricelist'],
                    'warehouse' => $producto['warehouse'],
                ]
            );
        }

        $productosPorCodigo = Product::pluck('id', 'codigo');

        foreach (self::PRECIOS as $precio) {
            PriceListProduct::updateOrCreate(
                [
                    'price_list_id' => $listasPorCodigo[$precio['lista']],
                    'product_id' => $productosPorCodigo[$precio['codigo']],
                ],
                [
                    'precio_unitario' => $this->normalizarPrecio($precio['precio_unitario']),
                    'iva' => $precio['iva'],
                ]
            );
        }

        foreach (self::STOCK as $stock) {
            WarehouseProduct::updateOrCreate(
                [
                    'warehouse_id' => $depositosPorCodigo[$stock['deposito']],
                    'product_id' => $productosPorCodigo[$stock['codigo']],
                ],
                [
                    'stock' => (int) round($this->normalizarNumero($stock['stock'])),
                ]
            );
        }
    }

    /**
     * Acepta números, strings con punto decimal ("334.336") o coma
     * decimal ("147,10") y devuelve siempre un float con punto decimal.
     */
    private function normalizarPrecio(int|float|string $valor): float
    {
        return round($this->normalizarNumero($valor), 2);
    }

    private function normalizarNumero(int|float|string $valor): float
    {
        if (is_int($valor) || is_float($valor)) {
            return (float) $valor;
        }

        $valor = trim($valor);

        // Si tiene coma pero no punto, la coma es el separador decimal ("147,10").
        if (str_contains($valor, ',') && ! str_contains($valor, '.')) {
            $valor = str_replace(',', '.', $valor);
        } else {
            // Si tiene ambos (formato "1.234,56"), el punto es separador de miles.
            if (str_contains($valor, ',') && str_contains($valor, '.')) {
                $valor = str_replace('.', '', $valor);
                $valor = str_replace(',', '.', $valor);
            }
        }

        return (float) $valor;
    }
}
