<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rutas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique();
            $table->string('nombre', 150);
            $table->foreignId('zona_id')->constrained('zonas')->onDelete('restrict');
            $table->text('descripcion')->nullable();
            
            // Punto de partida
            $table->string('punto_partida_nombre', 200);
            $table->decimal('punto_partida_latitud', 10, 7)->nullable();
            $table->decimal('punto_partida_longitud', 10, 7)->nullable();
            
            // Punto de llegada/fin
            $table->string('punto_fin_nombre', 200);
            $table->decimal('punto_fin_latitud', 10, 7)->nullable();
            $table->decimal('punto_fin_longitud', 10, 7)->nullable();
            
            // Trayecto completo (opcional)
            $table->json('trayecto')->nullable()->comment('Array de coordenadas del recorrido completo');
            
            $table->decimal('distancia_km', 8, 2)->nullable()->comment('Distancia en kilómetros');
            $table->integer('tiempo_estimado_minutos')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rutas');
    }
};
