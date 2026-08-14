<?php

use App\Http\Controllers\Api\ErpController;
use App\Http\Controllers\Api\HomeBannerController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductMediaController;
use App\Http\Controllers\Api\SaleController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| - GET    /api/products                           -> listado de productos (nombre, precio, stock, media)
| - GET    /api/products/random                     -> productos al azar (carrusel de accesos rápidos)
| - GET    /api/home-banners                        -> banners del home (advertisings) activos
| - POST   /api/products/{product}/media           -> agrega un medio/imagen a un producto
| - DELETE /api/products/{product}/media/{media}   -> elimina un medio de un producto
| - GET    /api/sales                              -> historial de ventas (listado con detalle de items)
| - POST   /api/sales                              -> cierre de compra (crea la venta + items)
|
| Consulta externa para ERP (protegida por el middleware erp.auth; requiere el
| parámetro "authorization" en el body):
| - POST /api/erp/pending-sales -> ids de las ventas sin sincronizar (sincronizado_erp = 0)
| - POST /api/erp/detail        -> detalle completo de una venta (requiere "id")
| - POST /api/erp/process       -> marca una venta como sincronizada con el ERP (requiere "id")
|
*/

Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/random', [ProductController::class, 'random']);
Route::get('/home-banners', [HomeBannerController::class, 'index']);

Route::post('/products/{product}/media', [ProductMediaController::class, 'store']);
Route::delete('/products/{product}/media/{media}', [ProductMediaController::class, 'destroy']);

Route::get('/sales', [SaleController::class, 'index']);
Route::post('/sales', [SaleController::class, 'store']);

Route::prefix('erp')->middleware('erp.auth')->group(function () {
    Route::post('/pending-sales', [ErpController::class, 'POST_pendingSales']);
    Route::post('/detail', [ErpController::class, 'POST_saleDetail']);
    Route::post('/process', [ErpController::class, 'POST_process']);
});
