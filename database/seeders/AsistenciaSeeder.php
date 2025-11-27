<?php

namespace Database\Seeders;

use App\Models\Personal;
use App\Models\Asistencia;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class AsistenciaSeeder extends Seeder
{
    public function run(): void
    {
        $personal = Personal::where('activo', true)->get();
        
        // Generar asistencias para los últimos 7 días
        for ($i = 6; $i >= 0; $i--) {
            $fecha = Carbon::today()->subDays($i);
            
            // Saltar domingos
            if ($fecha->dayOfWeek === Carbon::SUNDAY) {
                continue;
            }
            
            foreach ($personal as $persona) {
                // 90% de probabilidad de asistencia
                $estado = rand(1, 10) <= 9 ? 'presente' : 'ausente';
                
                if ($estado === 'presente') {
                    Asistencia::create([
                        'personal_id' => $persona->id,
                        'fecha' => $fecha,
                        'hora_entrada' => $fecha->copy()->setTime(7, rand(0, 30)),
                        'hora_salida' => $fecha->copy()->setTime(16, rand(0, 30)),
                        'estado' => 'presente',
                        'observaciones' => null,
                    ]);
                } else {
                    Asistencia::create([
                        'personal_id' => $persona->id,
                        'fecha' => $fecha,
                        'hora_entrada' => null,
                        'hora_salida' => null,
                        'estado' => 'ausente',
                        'observaciones' => 'Inasistencia',
                    ]);
                }
            }
        }
    }
}
