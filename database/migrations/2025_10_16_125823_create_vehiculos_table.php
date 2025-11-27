<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehiculos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique();
            $table->string('placa', 20)->unique();
            $table->foreignId('marca_id')->constrained('marcas')->onDelete('restrict');
            $table->foreignId('modelo_id')->constrained('modelos')->onDelete('restrict');
            $table->foreignId('tipo_vehiculo_id')->constrained('tipos_vehiculo')->onDelete('restrict');
            $table->foreignId('color_id')->constrained('colores')->onDelete('restrict');
            $table->year('anio');
            $table->string('numero_motor', 100)->nullable();
            $table->string('numero_chasis', 100)->nullable();
            $table->decimal('capacidad_carga', 8, 2)->nullable()->comment('En toneladas');
            $table->text('observaciones')->nullable();
            $table->boolean('disponible')->default(true);
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehiculos');
    }
};