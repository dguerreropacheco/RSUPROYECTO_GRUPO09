<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('horarios_mantenimiento', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mantenimiento_id')->constrained('mantenimientos')->onDelete('cascade');
            $table->foreignId('vehiculo_id')->constrained('vehiculos')->onDelete('restrict');
            $table->foreignId('responsable_id')->constrained('personal')->onDelete('restrict');
            $table->enum('tipo_mantenimiento', ['Preventivo', 'Limpieza', 'Reparación']);
            $table->enum('dia_semana', ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo']);
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->timestamps();
            
            // Índices para validaciones de solapamiento
            $table->index(['mantenimiento_id', 'dia_semana', 'vehiculo_id']);
            $table->index(['vehiculo_id', 'dia_semana']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('horarios_mantenimiento');
    }
};









