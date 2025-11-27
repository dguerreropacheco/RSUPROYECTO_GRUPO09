<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contratos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('personal_id')->constrained('personal')->onDelete('cascade');
            $table->enum('tipo_contrato', ['permanente', 'temporal'])->comment('Permanente o Temporal de 3 meses');
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable()->comment('NULL para permanentes');
            $table->text('observaciones')->nullable();
            $table->boolean('activo')->default(true)->comment('Solo un contrato activo por persona');
            $table->timestamps();
            $table->softDeletes();
            
            // Índice para optimizar búsquedas de contratos vigentes
            $table->index(['personal_id', 'activo', 'fecha_inicio', 'fecha_fin']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contratos');
    }
};
