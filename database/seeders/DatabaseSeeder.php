<?php


namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Seeders del módulo de vehículos
        $this->call([
            MarcaSeeder::class,
            TipoVehiculoSeeder::class,
            ColorSeeder::class,
            ModeloSeeder::class,
            VehiculoSeeder::class,
        ]);

        // Seeders del módulo de personal
        $this->call([
            FuncionSeeder::class,
            PersonalSeeder::class,
            ContratoSeeder::class,
            VacacionesSeeder::class,
            AsistenciaSeeder::class,
        ]);
    }
}