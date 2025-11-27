<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('zonas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique();
            $table->string('nombre', 150);
            $table->foreignId('distrito_id')->constrained('distritos')->onDelete('restrict');
            $table->text('descripcion')->nullable();
            $table->json('perimetro')->nullable()->comment('Coordenadas del perímetro en formato GeoJSON');
            $table->decimal('area', 10, 2)->nullable()->comment('Área en km²');
            $table->integer('poblacion_estimada')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zonas');
    }
};