<?php

namespace Database\Seeders;

use App\Models\Marca;
use App\Models\Modelo;
use Illuminate\Database\Seeder;

class ModeloSeeder extends Seeder
{
    public function run(): void
    {
        $modelos = [
            // Volvo
            ['marca' => 'Volvo', 'nombre' => 'FM 370', 'descripcion' => 'Camión pesado para recolección'],
            ['marca' => 'Volvo', 'nombre' => 'FE 280', 'descripcion' => 'Camión mediano'],
            
            // Mercedes-Benz
            ['marca' => 'Mercedes-Benz', 'nombre' => 'Atego 1726', 'descripcion' => 'Camión de distribución urbana'],
            ['marca' => 'Mercedes-Benz', 'nombre' => 'Accelo 1016', 'descripcion' => 'Camión ligero'],
            
            // Hino
            ['marca' => 'Hino', 'nombre' => 'Serie 300', 'descripcion' => 'Camión liviano para ciudad'],
            ['marca' => 'Hino', 'nombre' => 'Serie 500', 'descripcion' => 'Camión mediano'],
            ['marca' => 'Hino', 'nombre' => 'GH', 'descripcion' => 'Camión pesado'],
            
            // Hyundai
            ['marca' => 'Hyundai', 'nombre' => 'HD78', 'descripcion' => 'Camión ligero'],
            ['marca' => 'Hyundai', 'nombre' => 'Mighty', 'descripcion' => 'Camión de carga'],
            
            // JAC
            ['marca' => 'JAC', 'nombre' => 'N Series', 'descripcion' => 'Camión ligero económico'],
            ['marca' => 'JAC', 'nombre' => 'K Series', 'descripcion' => 'Camión mediano'],
            
            // Isuzu
            ['marca' => 'Isuzu', 'nombre' => 'NKR', 'descripcion' => 'Camión ligero urbano'],
            ['marca' => 'Isuzu', 'nombre' => 'FVR', 'descripcion' => 'Camión pesado'],
            
            // Scania
            ['marca' => 'Scania', 'nombre' => 'P 320', 'descripcion' => 'Camión para distribución'],
            ['marca' => 'Scania', 'nombre' => 'G 410', 'descripcion' => 'Camión de construcción'],
        ];

        foreach ($modelos as $modeloData) {
            $marca = Marca::where('nombre', $modeloData['marca'])->first();
            
            if ($marca) {
                Modelo::create([
                    'marca_id' => $marca->id,
                    'nombre' => $modeloData['nombre'],
                    'descripcion' => $modeloData['descripcion'],
                    'activo' => true,
                ]);
            }
        }
    }
}
