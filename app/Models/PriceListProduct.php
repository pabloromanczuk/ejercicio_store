<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PriceListProduct extends Model
{
    use HasFactory;

    protected $table = 'price_list_product';

    protected $fillable = [
        'price_list_id',
        'product_id',
        'precio_unitario',
        'iva',
    ];

    protected $casts = [
        'precio_unitario' => 'decimal:2',
        'iva' => 'decimal:2',
    ];

    public function priceList(): BelongsTo
    {
        return $this->belongsTo(PriceList::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
