<?php

namespace Database\Seeders;

use App\Models\Personal;
use App\Models\Contrato;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ContratoSeeder extends Seeder
{
    public function run(): void
    {
        $personal = Personal::all();

        $contratos = [
            // Conductores con contrato permanente
            [
                'dni' => '12345678',
                'tipo_contrato' => 'permanente',
                'fecha_inicio' => '2020-01-15',
                'fecha_fin' => null,
                'observaciones' => 'Contrato permanente desde el inicio del programa',
            ],
            [
                'dni' => '23456789',
                'tipo_contrato' => 'permanente',
                'fecha_inicio' => '2021-03-01',
                'fecha_fin' => null,
                'observaciones' => 'Contrato permanente',
            ],
            [
                'dni' => '34567890',
                'tipo_contrato' => 'temporal',
                'fecha_inicio' => '2024-10-01',
                'fecha_fin' => '2024-11-31',
                'observaciones' => 'Contrato temporal por 2 meses',
            ],
            [
                'dni' => '45678901',
                'tipo_contrato' => 'permanente',
                'fecha_inicio' => '2022-06-15',
                'fecha_fin' => null,
                'observaciones' => 'Contrato permanente',
            ],

            // Ayudantes
            [
                'dni' => '56789012',
                'tipo_contrato' => 'permanente',
                'fecha_inicio' => '2021-08-01',
                'fecha_fin' => null,
                'observaciones' => 'Contrato permanente',
            ],
            [
                'dni' => '67890123',
                'tipo_contrato' => 'temporal',
                'fecha_inicio' => '2024-09-01',
                'fecha_fin' => '2024-10-30',
                'observaciones' => 'Contrato temporal por 2 meses',
            ],
            [
                'dni' => '78901234',
                'tipo_contrato' => 'permanente',
                'fecha_inicio' => '2023-02-01',
                'fecha_fin' => null,
                'observaciones' => 'Contrato permanente',
            ],
            [
                'dni' => '89012345',
                'tipo_contrato' => 'temporal',
                'fecha_inicio' => '2024-10-15',
                'fecha_fin' => '2025-12-15',
                'observaciones' => 'Contrato temporal por 2 meses',
            ],

            // Supervisor
            [
                'dni' => '90123456',
                'tipo_contrato' => 'permanente',
                'fecha_inicio' => '2019-05-01',
                'fecha_fin' => null,
                'observaciones' => 'Supervisor desde el inicio',
            ],
        ];

        foreach ($contratos as $contratoData) {
            $persona = Personal::where('dni', $contratoData['dni'])->first();
            
            if ($persona) {
                Contrato::create([
                    'personal_id' => $persona->id,
                    'tipo_contrato' => $contratoData['tipo_contrato'],
                    'fecha_inicio' => $contratoData['fecha_inicio'],
                    'fecha_fin' => $contratoData['fecha_fin'],
                    'observaciones' => $contratoData['observaciones'],
                    'activo' => true,
                ]);
            }
        }
    }
}
