<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SaleItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'product_id' => $this->product_id,
            'codigo' => $this->codigo,
            'detalle' => $this->detalle,
            'cantidad' => $this->cantidad,
            'precio_unitario' => (float) $this->precio_unitario,
            'iva_pct' => (float) $this->iva_pct,
            'subtotal' => (float) $this->subtotal,
            'iva_monto' => (float) $this->iva_monto,
            'total' => (float) $this->total,
        ];
    }
}
