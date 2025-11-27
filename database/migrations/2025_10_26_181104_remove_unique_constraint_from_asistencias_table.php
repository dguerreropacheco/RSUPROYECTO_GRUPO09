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
        Schema::table('asistencias', function (Blueprint $table) {
            // Primero eliminar la foreign key
            $table->dropForeign(['personal_id']);
            
            // Luego eliminar la restricción única
            $table->dropUnique(['personal_id', 'fecha']);
            
            // Volver a crear la foreign key sin la restricción única
            $table->foreign('personal_id')->references('id')->on('personal')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asistencias', function (Blueprint $table) {
            // Eliminar la foreign key
            $table->dropForeign(['personal_id']);
            
            // Restaurar la restricción única
            $table->unique(['personal_id', 'fecha']);
            
            // Volver a crear la foreign key
            $table->foreign('personal_id')->references('id')->on('personal')->onDelete('cascade');
        });
    }
};
