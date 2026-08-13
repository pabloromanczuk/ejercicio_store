<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSaleRequest;
use App\Http\Resources\SaleResource;
use App\Models\Sale;
use App\Services\SaleService;
use App\Services\StockInsuficienteException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SaleController extends Controller
{
    public function __construct(private readonly SaleService $sales)
    {
    }

    /**
     * Historial de compras: todas las ventas con su detalle, más recientes primero.
     */
    public function index(): AnonymousResourceCollection
    {
        $ventas = Sale::with('items')->orderByDesc('created_at')->get();

        return SaleResource::collection($ventas);
    }

    public function store(StoreSaleRequest $request): JsonResponse
    {
        try {
            $sale = $this->sales->checkout($request->validated('items'));
        } catch (StockInsuficienteException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'error' => 'stock_insuficiente',
            ], 422);
        }

        return SaleResource::make($sale)
            ->response()
            ->setStatusCode(201);
    }

    public function show(Sale $sale): SaleResource
    {
        return SaleResource::make($sale->load('items'));
    }
}
