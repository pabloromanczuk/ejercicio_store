<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();

            // Número de comprobante propio del simulacro, útil como referencia
            // legible para el ERP externo (independiente del id autoincremental).
            $table->string('numero')->unique();

            $table->decimal('subtotal', 12, 2);   // suma de importes sin IVA
            $table->decimal('iva_total', 12, 2);  // suma de IVA discriminado
            $table->decimal('total', 12, 2);      // subtotal + iva_total

            // Estado de la venta dentro del simulacro (no hay pagos/envíos reales).
            $table->enum('estado', ['confirmada', 'cancelada'])->default('confirmada');

            // Campos pensados para la integración con el ERP externo: permiten
            // que un job/consumer externo marque cuándo tomó esta venta sin
            // tener que borrarla ni acoplar este sistema al ERP.
            $table->boolean('sincronizado_erp')->default(false);
            $table->timestamp('sincronizado_erp_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
