<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Aplicamos cambio de la columna stock por warehouse
            $table->dropColumn('stock');
            $table->string('warehouse')->nullable()->after('pricelist');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('warehouse');
            $table->unsignedInteger('stock')->default(0)->after('pricelist');
        });
    }
};
