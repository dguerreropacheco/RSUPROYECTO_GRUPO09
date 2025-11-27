<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    if (!Schema::hasTable('programacion_personal')) {
        Schema::create('programacion_personal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('programacion_id')->constrained('programaciones')->onDelete('cascade');
            $table->foreignId('personal_id')->constrained('personal')->onDelete('cascade');
            $table->date('fecha_dia');
            $table->timestamps();
            
            // ✅ Índice único correcto
            $table->unique(['programacion_id', 'personal_id'], 'programacion_personal_unique');
            
            // ✅ AGREGAR: Índice para búsquedas por fecha
            $table->index(['fecha_dia', 'personal_id'], 'programacion_personal_fecha_personal_idx');
        });
    }
}
    public function down(): void
    {
        Schema::dropIfExists('programacion_personal');
    }
};
