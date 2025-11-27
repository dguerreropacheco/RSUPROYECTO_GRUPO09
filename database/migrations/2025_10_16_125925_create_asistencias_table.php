<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asistencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('personal_id')->constrained('personal')->onDelete('cascade');
            $table->date('fecha');
            $table->time('hora_entrada')->nullable();
            $table->time('hora_salida')->nullable();
            $table->enum('estado', ['presente', 'ausente', 'tardanza', 'permiso', 'vacaciones'])->default('presente');
            $table->text('observaciones')->nullable();
            $table->timestamps();
            
            // Una asistencia por día por persona
            $table->unique(['personal_id', 'fecha']);
            
            // Índice para búsquedas rápidas
            $table->index(['fecha', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asistencias');
    }
};