<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeBanner extends Model
{
    use HasFactory;

    /** Carpeta pública donde viven las imágenes de los banners del home. */
    public const DIRECTORIO = 'assets/img/home-banners';

    protected $fillable = [
        'image',
        'title',
        'subtitle',
        'link',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * URL pública del archivo de imagen del banner.
     */
    public function getUrlAttribute(): string
    {
        return asset(self::DIRECTORIO.'/'.$this->image);
    }

    /**
     * Solo banners activos, ordenados por sort_order.
     */
    public function scopeActivos(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->orderBy('sort_order');
    }
}
