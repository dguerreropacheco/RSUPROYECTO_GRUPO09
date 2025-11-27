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
        Schema::create('grupospersonal', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 255);
            $table->foreignId('zona_id')->constrained('zonas')->onDelete('cascade');
            $table->foreignId('turno_id')->constrained('turnos')->onDelete('cascade');
            $table->foreignId('vehiculo_id')->constrained('vehiculos')->onDelete('cascade');
            $table->string('dias', 255)->comment('Días de la semana para este grupo (ej: Lunes, Martes, Miércoles)');
            $table->tinyInteger('estado')->default(1)->comment('Estado del grupo (ej: 1=Activo, 0=Inactivo)');
            $table->timestamps();
            $table->softDeletes();
            
            // Índices
            $table->index(['zona_id', 'turno_id', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grupospersonal');
    }
};
