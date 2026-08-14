<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SaleResource extends JsonResource
{
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
