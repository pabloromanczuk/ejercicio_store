<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            // "codigo" viene del sistema origen (planilla / ERP), lo mantenemos
            // como identificador de negocio, separado de la PK interna.
            $table->unsignedInteger('codigo')->unique();
            $table->string('detalle');

            // decimal(12,2) alcanza para moneda local; los datos de origen
            // vienen sucios (string, coma decimal, etc.) y se normalizan en el seeder.
            $table->decimal('precio_unitario', 12, 2);

            // porcentaje de IVA aplicado a este producto (21, 13, 10.5, 0...)
            $table->decimal('iva', 5, 2)->default(0);

            $table->unsignedInteger('stock')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
