<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PriceList extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigo',
        'nombre',
        'version',
        'vigencia_desde',
        'vigencia_hasta',
    ];

    protected $casts = [
        'vigencia_desde' => 'date',
        'vigencia_hasta' => 'date',
    ];

    /**
     * Productos asignados a esta lista, con su precio e IVA en el pivote.
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)
            ->withPivot(['precio_unitario', 'iva'])
            ->withTimestamps();
    }
}
