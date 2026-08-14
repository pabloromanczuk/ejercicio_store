<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends Controller
{
    /**
     * Listado paginado de productos con búsqueda y filtros (server-side).
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $limit = (int) $request->input('limit', 10);
        $limit = max(1, min(50, $limit));

        // select('products.*') para evitar colisiones de columnas con las subconsultas de precio/stock vigentes.
        $query = Product::with(['precioVigente', 'stockVigente', 'media'])
            ->select('products.*');

        $search = trim((string) $request->input('search', ''));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('products.detalle', 'like', "%{$search}%")
                    ->orWhereRaw('CAST(products.codigo AS CHAR) LIKE ?', ["%{$search}%"]);
            });
        }

        $soloDisponibles = $request->boolean('disponible');

        $sort = (string) $request->input('sort', 'detalle');
        $order = strtolower((string) $request->input('order', 'asc'));
        $order = in_array($order, ['asc', 'desc'], true) ? $order : 'asc';

        $necesitaPrecio = $soloDisponibles || $sort === 'price';
        $necesitaStock = $soloDisponibles || $sort === 'stock';

        if ($necesitaPrecio) {
            $query->conPrecioVigente();
        }
        if ($necesitaStock) {
            $query->conStockVigente();
        }

        if ($soloDisponibles) {
            $query->whereNotNull('plp_vigente.id')
                ->whereNotNull('wp_vigente.id');
        }

        if ($sort === 'price') {
            $query->orderByRaw("plp_vigente.precio_unitario IS NULL, plp_vigente.precio_unitario {$order}");
        } elseif ($sort === 'stock') {
            $query->orderByRaw("wp_vigente.stock IS NULL, wp_vigente.stock {$order}");
        } else {
            $query->orderBy('detalle', $order);
        }

        $productos = $query->paginate($limit);

        return ProductResource::collection($productos);
    }

    /**
     * Productos al azar para el carrusel de exhibición (accesos rápidos del PDP).
     */
    public function random(Request $request): AnonymousResourceCollection
    {
        $limit = (int) $request->input('limit', 8);
        $limit = max(1, min(20, $limit));

        $query = Product::with(['precioVigente', 'stockVigente', 'media'])
            ->select('products.*')
            ->conPrecioVigente()
            ->conStockVigente()
            ->whereNotNull('plp_vigente.id')
            ->whereNotNull('wp_vigente.id')
            ->inRandomOrder();

        if ($excluir = (int) $request->input('excluir')) {
            $query->where('products.id', '!=', $excluir);
        }

        $productos = $query->limit($limit)->get();

        return ProductResource::collection($productos);
    }
}
