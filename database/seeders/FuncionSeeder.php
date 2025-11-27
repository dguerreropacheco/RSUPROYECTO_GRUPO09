<?php
namespace Database\Seeders;

use App\Models\Funcion;
use Illuminate\Database\Seeder;

class FuncionSeeder extends Seeder
{
    public function run(): void
    {
        $funciones = [
            // Funciones predefinidas (no se pueden eliminar)
            [
                'nombre' => 'Conductor',
                'descripcion' => 'Responsable de conducir el vehículo de recolección',
                'es_predefinida' => true,
                'activo' => true,
            ],
            [
                'nombre' => 'Ayudante',
                'descripcion' => 'Asiste al conductor en la recolección de residuos',
                'es_predefinida' => true,
                'activo' => true,
            ],
            // Funciones adicionales
            [
                'nombre' => 'Supervisor',
                'descripcion' => 'Supervisa las operaciones de recolección',
                'es_predefinida' => false,
                'activo' => true,
            ],
            [
                'nombre' => 'Mecánico',
                'descripcion' => 'Encargado del mantenimiento de vehículos',
                'es_predefinida' => false,
                'activo' => true,
            ],
            [
                'nombre' => 'Coordinador',
                'descripcion' => 'Coordina rutas y programación',
                'es_predefinida' => false,
                'activo' => true,
            ],
        ];

        foreach ($funciones as $funcion) {
            Funcion::create($funcion);
        }
    }
}