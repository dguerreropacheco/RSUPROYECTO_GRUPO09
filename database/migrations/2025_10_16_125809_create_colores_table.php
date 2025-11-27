<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('colores', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 50);
            $table->string('codigo_rgb', 7); // Formato: #FFFFFF
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('colores');
    }
};