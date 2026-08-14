<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('home_banners', function (Blueprint $table) {
            $table->id();

            // Nombre del archivo de imagen del banner. Las imágenes viven en
            // public/assets/img/home-banners (una por registro).
            $table->string('image');

            // Textos que se muestran sobre el banner (control dinámico).
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();

            // URL opcional a la que lleva el banner al hacer clic.
            $table->string('link')->nullable();

            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('home_banners');
    }
};
