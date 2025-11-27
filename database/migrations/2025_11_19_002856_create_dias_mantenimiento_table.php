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
        Schema::create('dias_mantenimiento', function (Blueprint $table) {
            $table->id();
            $table->foreignId('horario_mantenimiento_id')->constrained('horarios_mantenimiento')->onDelete('cascade');
            $table->date('fecha');
            $table->text('observacion')->nullable();
            $table->string('imagen')->nullable();
            $table->boolean('realizado')->default(false);
            $table->timestamps();
            
            // Índices
            $table->index('horario_mantenimiento_id');
            $table->index('fecha');
            $table->unique(['horario_mantenimiento_id', 'fecha']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dias_mantenimiento');
    }
};

