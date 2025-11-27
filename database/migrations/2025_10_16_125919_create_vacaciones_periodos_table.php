<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vacaciones_periodos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vacaciones_id')->constrained('vacaciones')->onDelete('cascade');
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->integer('dias_utilizados');
            $table->enum('estado', ['programado', 'en_curso', 'finalizado', 'cancelado'])->default('programado');
            $table->text('observaciones')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vacaciones_periodos');
    }
};