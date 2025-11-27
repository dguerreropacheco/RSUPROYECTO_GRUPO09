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
        Schema::create('confgrupos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grupopersonal_id')
                ->constrained('grupospersonal')
                ->onDelete('cascade')
                ->comment('Clave foránea a la tabla grupospersonal');
            $table->foreignId('personal_id')
                ->constrained('personal')
                ->onDelete('cascade')
                ->comment('Clave foránea a la tabla personal');
            $table->timestamps();
            
            // Índice único para evitar duplicados
            $table->unique(['grupopersonal_id', 'personal_id'], 'confgrupos_grupo_personal_unique');
            
            // Índices adicionales para mejorar consultas
            $table->index('grupopersonal_id');
            $table->index('personal_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('confgrupos');
    }
};
