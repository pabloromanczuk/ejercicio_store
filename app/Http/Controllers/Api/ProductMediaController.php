<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductMediaRequest;
use App\Http\Resources\ProductMediaResource;
use App\Models\Product;
use App\Models\ProductMedia;
use Illuminate\Http\JsonResponse;

class ProductMediaController extends Controller
{
    public function store(StoreProductMediaRequest $request, Product $product): ProductMediaResource
    {
        $data = $request->validated();

        $media = $product->media()->create([
            'type' => $data['type'] ?? ProductMedia::TYPE_IMAGE,
            'path' => $data['path'],
            'sort_order' => $data['sort_order'] ?? $product->media()->max('sort_order') + 1,
            'is_primary' => $data['is_primary'] ?? $product->media()->doesntExist(),
        ]);

        return ProductMediaResource::make($media);
    }

    public function destroy(Product $product, ProductMedia $media): JsonResponse
    {
        // Evita borrar un medio que no pertenezca a este producto.
        abort_unless($media->product_id === $product->id, 404);

        $media->delete();

        return response()->json(['message' => 'Imagen eliminada correctamente.']);
    }
}
