<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductMedia extends Model
{
    use HasFactory;

    public const TYPE_IMAGE = 'image';

    /**
     * Ruta relativa del fallback que se usa cuando un producto no tiene media.
     */
    public const FALLBACK_PATH = 'assets/img/products/fallback.png';

    /**
     * URL pública del fallback de producto.
     */
    public static function fallbackUrl(): string
    {
        return asset(self::FALLBACK_PATH);
    }

    protected $fillable = [
        'product_id',
        'type',
        'path',
        'sort_order',
        'is_primary',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_primary' => 'boolean',
    ];

    protected static function booted(): void
    {
        // Garantiza una única imagen principal por producto a nivel de
        // aplicación: al marcar un medio como primario, los demás del mismo
        // producto se apagan (is_primary = false).
        static::saving(function (ProductMedia $media) {
            if ($media->is_primary) {
                self::query()
                    ->where('product_id', $media->product_id)
                    ->when($media->exists, fn ($query) => $query->whereKeyNot($media->getKey()))
                    ->update(['is_primary' => false]);
            }
        });
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * URL pública generada dinámicamente a partir del path relativo.
     * No se persiste en la base de datos.
     */
    public function getUrlAttribute(): string
    {
        return asset($this->path);
    }
}
