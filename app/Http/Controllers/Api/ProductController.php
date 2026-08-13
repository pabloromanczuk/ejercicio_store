<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $productos = Product::with(['precioVigente', 'stockVigente', 'media'])->orderBy('detalle')->get();

        return ProductResource::collection($productos);
    }
}
