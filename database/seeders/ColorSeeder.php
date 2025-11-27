<?php

namespace Database\Seeders;

use App\Models\Color;
use Illuminate\Database\Seeder;

class ColorSeeder extends Seeder
{
    public function run(): void
    {
        $colores = [
            ['nombre' => 'Blanco', 'codigo_rgb' => '#FFFFFF', 'activo' => true],
            ['nombre' => 'Negro', 'codigo_rgb' => '#000000', 'activo' => true],
            ['nombre' => 'Gris', 'codigo_rgb' => '#808080', 'activo' => true],
            ['nombre' => 'Plata', 'codigo_rgb' => '#C0C0C0', 'activo' => true],
            ['nombre' => 'Rojo', 'codigo_rgb' => '#FF0000', 'activo' => true],
            ['nombre' => 'Azul', 'codigo_rgb' => '#0000FF', 'activo' => true],
            ['nombre' => 'Verde', 'codigo_rgb' => '#008000', 'activo' => true],
            ['nombre' => 'Amarillo', 'codigo_rgb' => '#FFFF00', 'activo' => true],
            ['nombre' => 'Naranja', 'codigo_rgb' => '#FFA500', 'activo' => true],
            ['nombre' => 'Beige', 'codigo_rgb' => '#F5F5DC', 'activo' => true],
            ['nombre' => 'Verde Oscuro', 'codigo_rgb' => '#006400', 'activo' => true],
            ['nombre' => 'Celeste', 'codigo_rgb' => '#87CEEB', 'activo' => true],
        ];

        foreach ($colores as $color) {
            Color::create($color);
        }
    }
}