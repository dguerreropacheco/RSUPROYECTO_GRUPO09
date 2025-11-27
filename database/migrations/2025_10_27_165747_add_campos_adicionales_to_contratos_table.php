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
            $table->decimal('salario', 10, 2)->nullable()->after('fecha_fin');
            $table->string('departamento', 100)->nullable()->after('salario');
            $table->integer('periodo_prueba')->nullable()->comment('En meses')->after('departamento');
            $table->text('motivo_terminacion')->nullable()->after('observaciones');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contratos', function (Blueprint $table) {
            $table->dropColumn(['salario', 'departamento', 'periodo_prueba', 'motivo_terminacion']);
        });
    }
};