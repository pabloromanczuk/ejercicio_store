<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sale_id')->constrained()->cascadeOnDelete();

            // FK "blanda": si el producto se llegara a borrar del catálogo,
            // el renglón de venta no debe perderse (es un comprobante histórico).
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();

            // Snapshot de los datos del producto al momento de la venta.
            // Es clave para el endpoint de ventas: el ERP necesita ver el precio
            // e IVA que efectivamente se facturaron, aunque el catálogo cambie después.
            $table->unsignedInteger('codigo');
            $table->string('detalle');
            $table->unsignedInteger('cantidad');
            $table->decimal('precio_unitario', 12, 2);
            $table->decimal('iva_pct', 5, 2);

            $table->decimal('subtotal', 12, 2); // cantidad * precio_unitario
            $table->decimal('iva_monto', 12, 2); // subtotal * iva_pct/100
            $table->decimal('total', 12, 2);     // subtotal + iva_monto

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_items');
    }
};
