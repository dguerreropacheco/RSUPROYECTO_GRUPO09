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
        Schema::table('horarios_mantenimiento', function (Blueprint $table) {
            // Crear índices con nombres cortos
            $table->index(['mantenimiento_id', 'dia_semana', 'vehiculo_id'], 'idx_horario_mant_dia_veh');
            $table->index(['vehiculo_id', 'dia_semana'], 'idx_vehiculo_dia');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('horarios_mantenimiento', function (Blueprint $table) {
            $table->dropIndex('idx_horario_mant_dia_veh');
            $table->dropIndex('idx_vehiculo_dia');
        });
    }
};








