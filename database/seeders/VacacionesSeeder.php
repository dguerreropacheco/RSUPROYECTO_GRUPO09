<?php

namespace Database\Seeders;

use App\Models\Personal;
use App\Models\Vacaciones;
use App\Models\VacacionesPeriodo;
use Illuminate\Database\Seeder;

class VacacionesSeeder extends Seeder
{
    public function run(): void
    {
        // Solo personal con contrato permanente tiene vacaciones
        $personalPermanente = Personal::whereHas('contratoActivo', function ($query) {
            $query->where('tipo_contrato', 'permanente');
        })->get();

        foreach ($personalPermanente as $persona) {
            // Vacaciones 2024
            $vacaciones2024 = Vacaciones::create([
                'personal_id' => $persona->id,
                'anio' => 2024,
                'dias_programados' => 15,
                'dias_pendientes' => 15,
            ]);

            // Crear un periodo de vacaciones de ejemplo
            VacacionesPeriodo::create([
                'vacaciones_id' => $vacaciones2024->id,
                'fecha_inicio' => '2024-07-15',
                'fecha_fin' => '2024-07-29',
                'dias_utilizados' => 15,
                'estado' => 'finalizado',
                'observaciones' => 'Vacaciones de medio año',
            ]);

            // Vacaciones 2025
            Vacaciones::create([
                'personal_id' => $persona->id,
                'anio' => 2025,
                'dias_programados' => 0,
                'dias_pendientes' => 30,
            ]);
        }
    }
}