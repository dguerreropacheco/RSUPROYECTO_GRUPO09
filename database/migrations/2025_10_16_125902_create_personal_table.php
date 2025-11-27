<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('personal', function (Blueprint $table) {
            $table->id();
            $table->string('dni', 8)->unique();
            $table->string('nombres', 100);
            $table->string('apellido_paterno', 100);
            $table->string('apellido_materno', 100);
            $table->date('fecha_nacimiento');
            $table->string('telefono', 20)->nullable();
            $table->string('email', 150)->unique()->nullable();
            $table->text('direccion')->nullable();
            $table->string('licencia_conducir', 20)->nullable();
            $table->date('fecha_vencimiento_licencia')->nullable();
            $table->string('foto')->nullable();
            $table->foreignId('funcion_id')->constrained('funciones')->onDelete('restrict');
            $table->string('clave', 255); // Para marcación de asistencia
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('personal');
    }
};