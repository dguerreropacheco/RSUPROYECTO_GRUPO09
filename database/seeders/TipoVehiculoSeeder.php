<?php

namespace Database\Seeders;

use App\Models\TipoVehiculo;
use Illuminate\Database\Seeder;

class TipoVehiculoSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
            [
                'nombre' => 'Camión Compactador',
                'descripcion' => 'Vehículo especializado para compactar residuos sólidos',
                'activo' => true,
            ],
            [
                'nombre' => 'Camión Baranda',
                'descripcion' => 'Camión con baranda para transporte de residuos voluminosos',
                'activo' => true,
            ],
            [
                'nombre' => 'Camión Tolva',
                'descripcion' => 'Camión con tolva volcadora para residuos',
                'activo' => true,
            ],
            [
                'nombre' => 'Camioneta',
                'descripcion' => 'Vehículo ligero para zonas de difícil acceso',
                'activo' => true,
            ],
            [
                'nombre' => 'Camión Cisterna',
                'descripcion' => 'Vehículo para limpieza y riego de calles',
                'activo' => true,
            ],
            [
                'nombre' => 'Motocarga',
                'descripcion' => 'Vehículo motorizado de tres ruedas para recolección menor',
                'activo' => true,
            ],
        ];

        foreach ($tipos as $tipo) {
            TipoVehiculo::create($tipo);
        }
    }
}
