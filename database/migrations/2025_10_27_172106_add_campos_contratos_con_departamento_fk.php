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
        Schema::table('contratos', function (Blueprint $table) {
            // 1. Eliminar la columna departamento (varchar) si existe
            if (Schema::hasColumn('contratos', 'departamento')) {
                $table->dropColumn('departamento');
            }
            
            // 2. Agregar departamento_id como FK
            $table->foreignId('departamento_id')->nullable()->after('periodo_prueba')
                  ->constrained('departamentos')
                  ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contratos', function (Blueprint $table) {
            // Eliminar FK y columna
            $table->dropForeign(['departamento_id']);
            $table->dropColumn('departamento_id');
            
            // Restaurar columna departamento original
            $table->string('departamento', 100)->nullable();
        });
    }
};