<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cambios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('programacion_id')
                  ->constrained('programaciones')
                  ->onDelete('cascade');
            
            $table->enum('tipo_cambio', ['turno', 'vehiculo', 'personal']);
            
            // IDs originales (para referencia interna)
            $table->string('valor_anterior')->nullable();
            $table->string('valor_nuevo')->nullable();
            
            // Nombres legibles (para mostrar en UI)
            $table->string('valor_anterior_nombre')->nullable();
            $table->string('valor_nuevo_nombre')->nullable();
            
            $table->foreignId('motivo_id')
                  ->nullable()
                  ->constrained('motivos')
                  ->onDelete('set null');
            
            $table->text('notas')->nullable();
            
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');
            
            $table->timestamps();
            
            // Índices para búsquedas
            $table->index(['programacion_id', 'tipo_cambio']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cambios');
    }
};

