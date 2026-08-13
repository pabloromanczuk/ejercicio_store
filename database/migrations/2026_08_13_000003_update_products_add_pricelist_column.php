<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Eliminamos las columnas precio_unitario e iva y agregamos la columna pricelist
            $table->dropColumn(['precio_unitario', 'iva']);
            $table->string('pricelist')->nullable()->after('detalle');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('pricelist');
            $table->decimal('precio_unitario', 12, 2)->default(0)->after('detalle');
            $table->decimal('iva', 5, 2)->default(0)->after('precio_unitario');
        });
    }
};
