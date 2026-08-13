<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero',
        'subtotal',
        'iva_total',
        'total',
        'estado',
        'sincronizado_erp',
        'sincronizado_erp_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'iva_total' => 'decimal:2',
        'total' => 'decimal:2',
        'sincronizado_erp' => 'boolean',
        'sincronizado_erp_at' => 'datetime',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }
}
