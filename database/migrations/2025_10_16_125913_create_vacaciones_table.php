<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vacaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('personal_id')->constrained('personal')->onDelete('cascade');
            $table->year('anio');
            $table->integer('dias_programados')->default(0)->comment('Días de vacaciones programadas');
            $table->integer('dias_pendientes')->default(30)->comment('Días pendientes por tomar');
            $table->timestamps();
            $table->softDeletes();
            
            // Un registro de vacaciones por año por persona
            $table->unique(['personal_id', 'anio']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vacaciones');
    }
};
