<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'product_id',
        'codigo',
        'detalle',
        'cantidad',
        'precio_unitario',
        'iva_pct',
        'subtotal',
        'iva_monto',
        'total',
    ];

    protected $casts = [
        'codigo' => 'integer',
        'cantidad' => 'integer',
        'precio_unitario' => 'decimal:2',
        'iva_pct' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'iva_monto' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
