<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('motivos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->text('descripcion')->nullable();
            $table->boolean('activo')->default(1);
            $table->timestamps();
            
            $table->index('activo');
        });

        // Insertar motivos predeterminados
        DB::table('motivos')->insert([
            [
                'nombre' => 'Contingencia operativa',
                'descripcion' => 'Cambio debido a una situación imprevista en la operación',
                'activo' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Falla de vehículo',
                'descripcion' => 'El vehículo asignado presenta fallas mecánicas',
                'activo' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Ausencia de personal',
                'descripcion' => 'El personal asignado no puede asistir',
                'activo' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Emergencia médica',
                'descripcion' => 'Personal con emergencia médica o de salud',
                'activo' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Reprogramación administrativa',
                'descripcion' => 'Cambio solicitado por la administración',
                'activo' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('motivos');
    }
};

