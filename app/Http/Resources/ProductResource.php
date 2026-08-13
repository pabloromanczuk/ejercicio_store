<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'codigo' => $this->codigo,
            'detalle' => $this->detalle,
            'pricelist' => $this->pricelist,
            'precio_unitario' => $this->precio_unitario,
            'iva' => $this->iva,
            'precio_con_iva' => $this->precio_con_iva,
            'warehouse' => $this->warehouse,
            'stock' => $this->stock,
            'media' => ProductMediaResource::collection($this->whenLoaded('media')),
            'primary_image_url' => $this->primary_image_url,
            // Indican si el producto tiene precio/stock resueltos correctamente
            // (lista de precios/depósito válidos y relación existente en el pivote).
            'tiene_precio' => $this->precioVigente !== null,
            'tiene_stock' => $this->stockVigente !== null,
        ];
    }
}
