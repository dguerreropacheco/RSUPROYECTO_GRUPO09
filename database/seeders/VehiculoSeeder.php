<?php

namespace Database\Seeders;

use App\Models\Vehiculo;
use App\Models\Marca;
use App\Models\Modelo;
use App\Models\TipoVehiculo;
use App\Models\Color;
use Illuminate\Database\Seeder;

class VehiculoSeeder extends Seeder
{
    public function run(): void
    {
        $vehiculos = [
            [
                'codigo' => 'RSU-001',
                'placa' => 'T1X-234',
                'marca' => 'Volvo',
                'modelo' => 'FM 370',
                'tipo' => 'Camión Compactador',
                'color' => 'Verde',
                'anio' => 2020,
                'numero_motor' => 'VM-FH12-2020-001',
                'numero_chasis' => 'VCH-2020-FM370-001',
                'capacidad_carga' => 12.5,
                'observaciones' => 'Vehículo en óptimas condiciones',
            ],
            [
                'codigo' => 'RSU-002',
                'placa' => 'ABC-123',
                'marca' => 'Hino',
                'modelo' => 'Serie 500',
                'tipo' => 'Camión Compactador',
                'color' => 'Verde',
                'anio' => 2019,
                'numero_motor' => 'HN-500-2019-002',
                'numero_chasis' => 'HCH-2019-500-002',
                'capacidad_carga' => 10.0,
                'observaciones' => null,
            ],
            [
                'codigo' => 'RSU-003',
                'placa' => 'XYZ-789',
                'marca' => 'Mercedes-Benz',
                'modelo' => 'Atego 1726',
                'tipo' => 'Camión Baranda',
                'color' => 'Blanco',
                'anio' => 2021,
                'numero_motor' => 'MB-AT-2021-003',
                'numero_chasis' => 'MBCH-2021-AT-003',
                'capacidad_carga' => 8.5,
                'observaciones' => 'Requiere mantenimiento programado',
            ],
            [
                'codigo' => 'RSU-004',
                'placa' => 'P4Q-567',
                'marca' => 'Hyundai',
                'modelo' => 'HD78',
                'tipo' => 'Camión Tolva',
                'color' => 'Amarillo',
                'anio' => 2022,
                'numero_motor' => 'HY-HD78-2022-004',
                'numero_chasis' => 'HYCH-2022-HD78-004',
                'capacidad_carga' => 6.0,
                'observaciones' => null,
            ],
            [
                'codigo' => 'RSU-005',
                'placa' => 'RST-456',
                'marca' => 'JAC',
                'modelo' => 'N Series',
                'tipo' => 'Camioneta',
                'color' => 'Rojo',
                'anio' => 2023,
                'numero_motor' => 'JC-NS-2023-005',
                'numero_chasis' => 'JCCH-2023-NS-005',
                'capacidad_carga' => 2.5,
                'observaciones' => 'Vehículo nuevo para zonas estrechas',
            ],
            [
                'codigo' => 'RSU-006',
                'placa' => 'UVW-321',
                'marca' => 'Isuzu',
                'modelo' => 'NKR',
                'tipo' => 'Camión Compactador',
                'color' => 'Verde Oscuro',
                'anio' => 2018,
                'numero_motor' => 'IZ-NKR-2018-006',
                'numero_chasis' => 'IZCH-2018-NKR-006',
                'capacidad_carga' => 7.5,
                'observaciones' => 'En revisión técnica',
                'disponible' => false,
            ],
        ];

        foreach ($vehiculos as $vehiculoData) {
            $marca = Marca::where('nombre', $vehiculoData['marca'])->first();
            $modelo = Modelo::where('nombre', $vehiculoData['modelo'])->first();
            $tipo = TipoVehiculo::where('nombre', $vehiculoData['tipo'])->first();
            $color = Color::where('nombre', $vehiculoData['color'])->first();

            if ($marca && $modelo && $tipo && $color) {
                Vehiculo::create([
                    'codigo' => $vehiculoData['codigo'],
                    'placa' => $vehiculoData['placa'],
                    'marca_id' => $marca->id,
                    'modelo_id' => $modelo->id,
                    'tipo_vehiculo_id' => $tipo->id,
                    'color_id' => $color->id,
                    'anio' => $vehiculoData['anio'],
                    'numero_motor' => $vehiculoData['numero_motor'],
                    'numero_chasis' => $vehiculoData['numero_chasis'],
                    'capacidad_carga' => $vehiculoData['capacidad_carga'],
                    'observaciones' => $vehiculoData['observaciones'],
                    'disponible' => $vehiculoData['disponible'] ?? true,
                    'activo' => true,
                ]);
            }
        }
    }
}