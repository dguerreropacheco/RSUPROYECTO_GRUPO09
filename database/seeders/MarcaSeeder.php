<?php

namespace Database\Seeders;

use App\Models\Marca;
use Illuminate\Database\Seeder;

class MarcaSeeder extends Seeder
{
    public function run(): void
    {
        $marcas = [
            [
                'nombre' => 'Volvo',
                'descripcion' => 'Fabricante sueco de vehículos comerciales y de construcción',
                'activo' => true,
            ],
            [
                'nombre' => 'Mercedes-Benz',
                'descripcion' => 'Fabricante alemán de vehículos comerciales',
                'activo' => true,
            ],
            [
                'nombre' => 'Hino',
                'descripcion' => 'Fabricante japonés de camiones',
                'activo' => true,
            ],
            [
                'nombre' => 'Hyundai',
                'descripcion' => 'Fabricante coreano de vehículos comerciales',
                'activo' => true,
            ],
            [
                'nombre' => 'JAC',
                'descripcion' => 'Fabricante chino de vehículos comerciales',
                'activo' => true,
            ],
            [
                'nombre' => 'Isuzu',
                'descripcion' => 'Fabricante japonés de camiones ligeros y medianos',
                'activo' => true,
            ],
            [
                'nombre' => 'Scania',
                'descripcion' => 'Fabricante sueco de camiones pesados',
                'activo' => true,
            ],
        ];

        foreach ($marcas as $marca) {
            Marca::create($marca);
        }
    }
}

