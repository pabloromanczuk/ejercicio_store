<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Sale;
use App\Models\WarehouseProduct;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SaleService
{
    /**
     * Cierra una compra: valida stock, calcula importes, persiste la venta
     * y sus items, y descuenta stock. Todo en una transacción para que
     * un fallo a mitad de camino no deje stock descontado sin venta (o viceversa).
     *
     * @param  array<int, array{product_id:int, cantidad:int}>  $items
     */
    public function checkout(array $items): Sale
    {
        return DB::transaction(function () use ($items) {
            // Bloqueamos las filas de producto involucradas para evitar
            // condiciones de carrera si dos compras del mismo producto
            // se cierran casi al mismo tiempo (sobreventa de stock).
            $productIds = collect($items)->pluck('product_id')->unique();

            // Bloqueamos las filas de producto y de stock (pivote) para evitar
            // condiciones de carrera si dos compras del mismo producto se cierran
            // casi al mismo tiempo (sobreventa de stock).
            Product::whereIn('id', $productIds)->lockForUpdate()->get();
            WarehouseProduct::whereIn('product_id', $productIds)->lockForUpdate()->get();

            $productos = Product::with(['precioVigente', 'stockVigente'])
                ->whereIn('id', $productIds)
                ->get()
                ->keyBy('id');

            $subtotalGeneral = 0;
            $ivaGeneral = 0;
            $totalGeneral = 0;
            $itemsParaGuardar = [];

            foreach ($items as $item) {
                /** @var Product $producto */
                $producto = $productos->get($item['product_id']);
                $cantidad = (int) $item['cantidad'];

                if (! $producto->hasStockDisponible($cantidad)) {
                    throw new StockInsuficienteException($producto->detalle, $producto->stock, $cantidad);
                }

                // El precio/IVA sale de la lista de precios vigente del producto.
                if (! $producto->precioVigente) {
                    throw new \RuntimeException(
                        "El producto '{$producto->detalle}' no tiene precio asignado en la lista '{$producto->pricelist}'."
                    );
                }

                $precioUnitario = (float) $producto->precio_unitario;
                $ivaPct = (float) $producto->iva;

                $subtotal = round($precioUnitario * $cantidad, 2);
                $ivaMonto = round($subtotal * $ivaPct / 100, 2);
                $total = round($subtotal + $ivaMonto, 2);

                $subtotalGeneral += $subtotal;
                $ivaGeneral += $ivaMonto;
                $totalGeneral += $total;

                $itemsParaGuardar[] = [
                    'product_id' => $producto->id,
                    'codigo' => $producto->codigo,
                    'detalle' => $producto->detalle,
                    'cantidad' => $cantidad,
                    'precio_unitario' => $precioUnitario,
                    'iva_pct' => $ivaPct,
                    'subtotal' => $subtotal,
                    'iva_monto' => $ivaMonto,
                    'total' => $total,
                ];

                // Descontamos stock de forma atómica en el pivote depósito-producto.
                if (! $producto->stockVigente) {
                    throw new \RuntimeException(
                        "El producto '{$producto->detalle}' no tiene stock asignado en el depósito '{$producto->warehouse}'."
                    );
                }
                $producto->stockVigente->decrement('stock', $cantidad);
            }

            $sale = Sale::create([
                'numero' => $this->generarNumero(),
                'subtotal' => round($subtotalGeneral, 2),
                'iva_total' => round($ivaGeneral, 2),
                'total' => round($totalGeneral, 2),
                'estado' => 'confirmada',
            ]);

            $sale->items()->createMany($itemsParaGuardar);

            return $sale->load('items');
        });
    }

    private function generarNumero(): string
    {
        // Formato simple y legible: VTA-YYYYMMDD-XXXXXX. No es un correlativo
        // estricto (no hace falta para el ejercicio) pero es único y ordenable.
        return 'VTA-'.now()->format('Ymd').'-'.strtoupper(Str::random(6));
    }
}
