<?php

namespace Database\Seeders;

use App\Models\PriceList;
use App\Models\PriceListProduct;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\WarehouseProduct;
use Illuminate\Database\Seeder;

class ProductosExtrasSeeder extends Seeder
{
    /**
     * [codigo, detalle, pricelist, warehouse]
     */
    private const PRODUCTOS = [
        [1001, 'Bananas', 'LISTA-PUBLICA', 'GENERAL'],
        [1002, 'Uvas', 'LISTA-PUBLICA', 'GENERAL'],
        [1003, 'Duraznos', 'LISTA-MAYORISTA', 'GENERAL'],
        [1004, 'Frutillas', 'LISTA-PUBLICA', 'GENERAL'],
        [1005, 'Kiwis', 'LISTA-PUBLICA', 'GENERAL'],
        [1006, 'Mandarinas', 'LISTA-PUBLICA', 'GENERAL'],
        [1007, 'Limones', 'LISTA-MAYORISTA', 'GENERAL'],
        [1008, 'Pomelos', 'LISTA-PUBLICA', 'GENERAL'],
        [1009, 'Melones', 'LISTA-PUBLICA', 'GENERAL'],
        [1010, 'Sandías', 'LISTA-MAYORISTA', 'GENERAL'],
        [1011, 'Zanahorias', 'LISTA-PUBLICA', 'GENERAL'],
        [1012, 'Papas', 'LISTA-PUBLICA', 'GENERAL'],
        [1013, 'Cebollas', 'LISTA-MAYORISTA', 'GENERAL'],
        [1014, 'Tomates', 'LISTA-PUBLICA', 'GENERAL'],
        [1015, 'Lechuga', 'LISTA-PUBLICA', 'GENERAL'],
        [1016, 'Pimientos', 'LISTA-PUBLICA', 'GENERAL'],
        [1017, 'Calabacines', 'LISTA-MAYORISTA', 'GENERAL'],
        [1018, 'Berenjenas', 'LISTA-PUBLICA', 'GENERAL'],
        [1019, 'Choclos', 'LISTA-PUBLICA', 'GENERAL'],
        [1020, 'Arvejas', 'LISTA-PUBLICA', 'GENERAL'],
        [1021, 'Manzanas Verdes', 'LISTA-PUBLICA', 'GENERAL'],
        [1022, 'Peras Williams', 'LISTA-MAYORISTA', 'GENERAL'],
        [1023, 'Ciruelas', 'LISTA-PUBLICA', 'GENERAL'],
        [1024, 'Damascos', 'LISTA-PUBLICA', 'GENERAL'],
        // Sin stock (no tiene fila en STOCK): demuestra el filtro "Disponibles".
        [1025, 'Acelgas', 'LISTA-PUBLICA', 'GENERAL'],
        // Sin precio (no tiene fila en PRECIOS): demuestra el filtro "Disponibles".
        [1026, 'Batatas', 'LISTA-PUBLICA', 'GENERAL'],
    ];

    /**
     * [codigo, lista, precio_unitario, iva] — algunos precios vienen "sucios"
     * (string con coma decimal) para conservar el estilo del seeder original.
     */
    private const PRECIOS = [
        [1001, 'LISTA-PUBLICA', 320.5, 21],
        [1002, 'LISTA-PUBLICA', '850,50', 21],
        [1003, 'LISTA-MAYORISTA', 420, 21],
        [1004, 'LISTA-PUBLICA', '990,00', 10.5],
        [1005, 'LISTA-PUBLICA', 540, 21],
        [1006, 'LISTA-PUBLICA', 360, 13],
        [1007, 'LISTA-MAYORISTA', 210, 21],
        [1008, 'LISTA-PUBLICA', 380, 21],
        [1009, 'LISTA-PUBLICA', 620, 21],
        [1010, 'LISTA-MAYORISTA', 740, 21],
        [1011, 'LISTA-PUBLICA', 180.5, 10.5],
        [1012, 'LISTA-PUBLICA', 150, 21],
        [1013, 'LISTA-MAYORISTA', '165.30', 21],
        [1014, 'LISTA-PUBLICA', 290, 21],
        [1015, 'LISTA-PUBLICA', 230, 10.5],
        [1016, 'LISTA-PUBLICA', 340, 21],
        [1017, 'LISTA-MAYORISTA', 260, 21],
        [1018, 'LISTA-PUBLICA', 275, 21],
        [1019, 'LISTA-PUBLICA', 195, 21],
        [1020, 'LISTA-PUBLICA', 310, 21],
        [1021, 'LISTA-PUBLICA', 480, 21],
        [1022, 'LISTA-MAYORISTA', 410, 21],
        [1023, 'LISTA-PUBLICA', 390, 21],
        [1024, 'LISTA-PUBLICA', 445, 21],
        // Acelgas tiene precio pero NO stock.
        [1025, 'LISTA-PUBLICA', 205, 21],
    ];

    /**
     * [codigo, deposito, stock] — algunos stocks vienen como string ("31.0").
     */
    private const STOCK = [
        [1001, 'GENERAL', 40],
        [1002, 'GENERAL', 25],
        [1003, 'GENERAL', 30],
        [1004, 'GENERAL', 18],
        [1005, 'GENERAL', 22],
        [1006, 'GENERAL', 35],
        [1007, 'GENERAL', 50],
        [1008, 'GENERAL', 15],
        [1009, 'GENERAL', 12],
        [1010, 'GENERAL', 10],
        [1011, 'GENERAL', '60.0'],
        [1012, 'GENERAL', 80],
        [1013, 'GENERAL', 70],
        [1014, 'GENERAL', 45],
        [1015, 'GENERAL', 28],
        [1016, 'GENERAL', 33],
        [1017, 'GENERAL', 24],
        [1018, 'GENERAL', 20],
        [1019, 'GENERAL', 26],
        [1020, 'GENERAL', 19],
        [1021, 'GENERAL', 32],
        [1022, 'GENERAL', 27],
        [1023, 'GENERAL', 21],
        [1024, 'GENERAL', 16],
        // Batatas tiene stock pero NO precio.
        [1026, 'GENERAL', 44],
    ];

    public function run(): void
    {
        $listasPorCodigo = PriceList::pluck('id', 'codigo');
        $depositosPorCodigo = Warehouse::pluck('id', 'codigo');

        foreach (self::PRODUCTOS as [$codigo, $detalle, $pricelist, $warehouse]) {
            Product::updateOrCreate(
                ['codigo' => $codigo],
                [
                    'detalle' => $detalle,
                    'pricelist' => $pricelist,
                    'warehouse' => $warehouse,
                ]
            );
        }

        $productosPorCodigo = Product::pluck('id', 'codigo');

        foreach (self::PRECIOS as [$codigo, $lista, $precio, $iva]) {
            PriceListProduct::updateOrCreate(
                [
                    'price_list_id' => $listasPorCodigo[$lista],
                    'product_id' => $productosPorCodigo[$codigo],
                ],
                [
                    'precio_unitario' => $this->normalizarPrecio($precio),
                    'iva' => $iva,
                ]
            );
        }

        foreach (self::STOCK as [$codigo, $deposito, $stock]) {
            WarehouseProduct::updateOrCreate(
                [
                    'warehouse_id' => $depositosPorCodigo[$deposito],
                    'product_id' => $productosPorCodigo[$codigo],
                ],
                [
                    'stock' => (int) round($this->normalizarNumero($stock)),
                ]
            );
        }
    }

    /**
     * Acepta números o strings con coma/punto decimal y devuelve un float.
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

        // Coma como separador decimal ("850,50") o punto ("165.30").
        if (str_contains($valor, ',') && ! str_contains($valor, '.')) {
            $valor = str_replace(',', '.', $valor);
        }

        return (float) $valor;
    }
}
