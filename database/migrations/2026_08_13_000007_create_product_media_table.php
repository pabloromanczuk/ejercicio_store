<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_media', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')->constrained()->cascadeOnDelete();

            // Tipo de contenido: por ahora "image"; pensado para admitir
            // luego "video" o "pdf" sin modificar la tabla products.
            $table->string('type')->default('image');

            // Ruta relativa al archivo dentro de public/ (ej: assets/img/products/x.jpg).
            // NO se guarda la URL completa (ni http:// ni asset()).
            $table->string('path');

            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_primary')->default(false);

            $table->timestamps();

            // Índices para las consultas habituales del store (orden y principal).
            $table->index(['product_id', 'sort_order']);
            $table->index(['product_id', 'is_primary']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_media');
    }
};
