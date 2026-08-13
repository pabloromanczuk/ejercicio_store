<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Endpoints de consulta externa para integración con un ERP.
 * Todos son POST y están protegidos por el middleware ErpApiAuth.
 */
class ErpController extends Controller
{
    public function POST_pendingSales(): JsonResponse
    {
        $ids = Sale::where('sincronizado_erp', false)
            ->orderBy('id')
            ->pluck('id')
            ->values();

        return response()->json($ids);
    }

    public function POST_saleDetail(Request $request): JsonResponse
    {
        $id = $this->validarId($request);
        if ($id === null) {
            return $this->error('El parámetro "id" es obligatorio y debe ser numérico.');
        }

        $sale = Sale::with(['items.product'])->find($id);
        if (! $sale) {
            return $this->error("No existe la venta con id {$id}.");
        }

        return response()->json($this->formatearVenta($sale));
    }

    public function POST_process(Request $request): JsonResponse
    {
        $id = $this->validarId($request);
        if ($id === null) {
            return $this->error('El parámetro "id" es obligatorio y debe ser numérico.');
        }

        $sale = Sale::find($id);
        if (! $sale) {
            return $this->error("No existe la venta con id {$id}.");
        }

        $sale->update([
            'sincronizado_erp' => true,
            'sincronizado_erp_at' => now(),
        ]);

        return response()->json([
            'success' => 'rws_ok',
            'id' => $sale->id,
            'sincronizado_erp' => true,
            'sincronizado_erp_at' => $sale->fresh()->sincronizado_erp_at?->toIso8601String(),
        ]);
    }

    /**
     * Estructura de la venta para el ERP: datos de la orden + items anidados.
     */
    private function formatearVenta(Sale $sale): array
    {
        /** @var Collection<int, SaleItem> $items */
        $items = $sale->items;

        $itemsFormateados = $items->map(function (SaleItem $item) {
            return [
                'product_id' => $item->product_id,
                'codigo' => $item->codigo,
                'detalle' => $item->detalle,
                'cantidad' => $item->cantidad,
                'precio_unitario' => (float) $item->precio_unitario,
                'iva_pct' => (float) $item->iva_pct,
                'subtotal' => (float) $item->subtotal,
                'iva_monto' => (float) $item->iva_monto,
                'total' => (float) $item->total,
                // Datos del catálogo al momento de la consulta (lista y depósito).
                'listaprecio' => $item->product?->pricelist,
                'warehouse' => $item->product?->warehouse,
            ];
        })->values();

        return [
            'id' => $sale->id,
            'numero' => $sale->numero,
            'estado' => $sale->estado,
            'moneda' => 'ARS',
            'fecha' => $sale->created_at?->toIso8601String(),
            'listaprecio' => $this->valorComun($itemsFormateados, 'listaprecio'),
            'warehouse' => $this->valorComun($itemsFormateados, 'warehouse'),
            'subtotal' => (float) $sale->subtotal,
            'iva_total' => (float) $sale->iva_total,
            'total' => (float) $sale->total,
            'cantidad_items' => $items->sum('cantidad'),
            'sincronizado_erp' => (bool) $sale->sincronizado_erp,
            'items' => $itemsFormateados,
        ];
    }

    /**
     * Devuelve el valor si todos los ítems coinciden en esa clave; si difieren
     * o no hay ítems, devuelve null.
     *
     * @param  Collection<int, array<string, mixed>>  $items
     */
    private function valorComun(Collection $items, string $clave): ?string
    {
        $valores = $items->pluck($clave)->unique()->filter()->values();

        return $valores->count() === 1 ? $valores->first() : null;
    }

    private function validarId(Request $request): ?int
    {
        $id = $request->input('id');

        if ($id === null || ! is_numeric($id) || (int) $id <= 0) {
            return null;
        }

        return (int) $id;
    }

    private function error(string $message): JsonResponse
    {
        return response()->json([
            'success' => 'rws_fail',
            'message' => $message,
        ]);
    }
}
