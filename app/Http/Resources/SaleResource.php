<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SaleResource extends JsonResource
{
    /**
     * Estructura pensada para que un integrador externo tome esta venta
     * y grabe la nota de venta en un ERP: incluye número de comprobante,
     * fecha, moneda, totales discriminados y el detalle línea por línea
     * con el snapshot de precio/IVA que efectivamente se vendió (no el
     * precio actual del catálogo, que puede haber cambiado).
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'numero' => $this->numero,
            'estado' => $this->estado,
            'moneda' => 'ARS',
            'fecha' => $this->created_at->toIso8601String(),
            'subtotal' => (float) $this->subtotal,
            'iva_total' => (float) $this->iva_total,
            'total' => (float) $this->total,
            'cantidad_items' => $this->items->sum('cantidad'),
            'items' => SaleItemResource::collection($this->whenLoaded('items')),
            'integracion_erp' => [
                'sincronizado' => (bool) $this->sincronizado_erp,
                'sincronizado_at' => $this->sincronizado_erp_at?->toIso8601String(),
            ],
        ];
    }
}
