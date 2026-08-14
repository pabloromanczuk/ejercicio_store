<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigo',
        'detalle',
        'pricelist',
        'warehouse',
    ];

    protected $casts = [
        'codigo' => 'integer',
    ];

    /**
     * Todas las asignaciones del producto a listas de precios (tabla pivote).
     */
    public function priceListProductos(): HasMany
    {
        return $this->hasMany(PriceListProduct::class);
    }

    /**
     * Fila de la tabla pivote que corresponde a la lista indicada en
     * products.pricelist (el precio/IVA vigente del producto).
     */
    public function precioVigente(): HasOne
    {
        return $this->hasOne(PriceListProduct::class, 'product_id')
            ->select('price_list_product.*')
            ->join('price_lists', 'price_lists.id', '=', 'price_list_product.price_list_id')
            ->join('products', 'products.id', '=', 'price_list_product.product_id')
            ->whereColumn('price_lists.codigo', 'products.pricelist');
    }

    /**
     * Todas las asignaciones del producto a depósitos (tabla pivote).
     */
    public function warehouseProductos(): HasMany
    {
        return $this->hasMany(WarehouseProduct::class);
    }

    /**
     * Fila de la tabla pivote que corresponde al depósito indicado en
     * products.warehouse (el stock vigente del producto).
     */
    public function stockVigente(): HasOne
    {
        return $this->hasOne(WarehouseProduct::class, 'product_id')
            ->select('warehouse_product.*')
            ->join('warehouses', 'warehouses.id', '=', 'warehouse_product.warehouse_id')
            ->join('products', 'products.id', '=', 'warehouse_product.product_id')
            ->whereColumn('warehouses.codigo', 'products.warehouse');
    }

    /**
     * Left join de la fila "vigente" de price_list_product (la que corresponde
     * a la lista indicada en products.pricelist). Permite filtrar y ordenar por
     * precio en el mismo query que lista los productos, sin depender del front.
     *
     * El join es 1:1: la fila se resuelve por products.pricelist, así que no
     * multiplica filas aunque el producto tenga filas en otras listas.
     */
    public function scopeConPrecioVigente(Builder $query): Builder
    {
        return $query
            ->leftJoin('price_list_product as plp_vigente', 'plp_vigente.product_id', '=', 'products.id')
            ->leftJoin('price_lists as pl_vigente', 'pl_vigente.id', '=', 'plp_vigente.price_list_id')
            ->whereColumn('pl_vigente.codigo', 'products.pricelist');
    }

    /**
     * Left join de la fila "vigente" de warehouse_product (la que corresponde
     * al depósito indicado en products.warehouse). Análogo a conPrecioVigente.
     */
    public function scopeConStockVigente(Builder $query): Builder
    {
        return $query
            ->leftJoin('warehouse_product as wp_vigente', 'wp_vigente.product_id', '=', 'products.id')
            ->leftJoin('warehouses as w_vigente', 'w_vigente.id', '=', 'wp_vigente.warehouse_id')
            ->whereColumn('w_vigente.codigo', 'products.warehouse');
    }

    /**
     * Todos los medios asociados al producto, ordenados por sort_order.
     */
    public function media(): HasMany
    {
        return $this->hasMany(ProductMedia::class)->orderBy('sort_order');
    }

    /**
     * Solo imágenes del producto, ordenadas por sort_order.
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductMedia::class)
            ->where('type', ProductMedia::TYPE_IMAGE)
            ->orderBy('sort_order');
    }

    /**
     * Imagen principal del producto (la marcada como is_primary, o la primera
     * si ninguna está marcada). Requiere que la relación media esté cargada
     * (usar with('media') para evitar N+1).
     */
    public function getPrimaryMediaAttribute(): ?ProductMedia
    {
        return $this->media->firstWhere('is_primary', true) ?? $this->media->first();
    }

    /**
     * URL de la imagen principal del producto; si no tiene media, devuelve
     * la URL del fallback. Centraliza el fallback para que los consumidores
     * (Blade, API, Store) no conozcan la estructura física de las imágenes.
     */
    public function getPrimaryImageUrlAttribute(): string
    {
        return $this->primaryMedia?->url ?? ProductMedia::fallbackUrl();
    }

    /**
     * Precio unitario vigente (0 si el producto no tiene lista asignada o
     * precio definido). Resuelto desde la lista indicada en pricelist.
     */
    public function getPrecioUnitarioAttribute(): float
    {
        return (float) ($this->precioVigente?->precio_unitario ?? 0);
    }

    /**
     * IVA vigente del producto según su lista de precios.
     */
    public function getIvaAttribute(): float
    {
        return (float) ($this->precioVigente?->iva ?? 0);
    }

    /**
     * Precio unitario ya incluyendo IVA (útil para mostrarlo en el listado).
     */
    public function getPrecioConIvaAttribute(): float
    {
        return round($this->precio_unitario * (1 + $this->iva / 100), 2);
    }

    /**
     * Stock vigente del producto en el depósito indicado en products.warehouse
     * (0 si no tiene depósito asignado o stock definido).
     */
    public function getStockAttribute(): int
    {
        return (int) ($this->stockVigente?->stock ?? 0);
    }

    public function hasStockDisponible(int $cantidad): bool
    {
        return $this->stock >= $cantidad;
    }
}
