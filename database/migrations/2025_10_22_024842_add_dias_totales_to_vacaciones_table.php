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
        Schema::table('vacaciones', function (Blueprint $table) {
            // Agregar campo dias_totales después de anio
            $table->integer('dias_totales')->default(30)
                  ->comment('Máximo 30 días por año')
                  ->after('anio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vacaciones', function (Blueprint $table) {
            $table->dropColumn('dias_totales');
        });
    }
};