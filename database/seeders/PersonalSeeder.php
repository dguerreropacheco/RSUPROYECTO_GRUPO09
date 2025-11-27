<?php

namespace Database\Seeders;

use App\Models\Personal;
use App\Models\Funcion;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PersonalSeeder extends Seeder
{
    public function run(): void
    {
        $funcionConductor = Funcion::where('nombre', 'Conductor')->first();
        $funcionAyudante = Funcion::where('nombre', 'Ayudante')->first();
        $funcionSupervisor = Funcion::where('nombre', 'Supervisor')->first();

        $personal = [
            // Conductores
            [
                'dni' => '12345678',
                'nombres' => 'Carlos Alberto',
                'apellido_paterno' => 'García',
                'apellido_materno' => 'Pérez',
                'fecha_nacimiento' => '1985-03-15',
                'telefono' => '987654321',
                'email' => 'carlos.garcia@jlo.gob.pe',
                'direccion' => 'Av. Luis Gonzales 123, José Leonardo Ortiz',
                'licencia_conducir' => 'A-IIB-12345678',
                'fecha_vencimiento_licencia' => '2026-12-31',
                'funcion_id' => $funcionConductor->id,
                'clave' => '1234', // Se encriptará automáticamente
                'activo' => true,
            ],
            [
                'dni' => '23456789',
                'nombres' => 'Miguel Ángel',
                'apellido_paterno' => 'Rodríguez',
                'apellido_materno' => 'Silva',
                'fecha_nacimiento' => '1990-07-22',
                'telefono' => '998877665',
                'email' => 'miguel.rodriguez@jlo.gob.pe',
                'direccion' => 'Jr. San Martín 456, José Leonardo Ortiz',
                'licencia_conducir' => 'A-IIB-23456789',
                'fecha_vencimiento_licencia' => '2027-06-30',
                'funcion_id' => $funcionConductor->id,
                'clave' => '1234',
                'activo' => true,
            ],
            [
                'dni' => '34567890',
                'nombres' => 'José Luis',
                'apellido_paterno' => 'Fernández',
                'apellido_materno' => 'Torres',
                'fecha_nacimiento' => '1988-11-10',
                'telefono' => '955443322',
                'email' => 'jose.fernandez@jlo.gob.pe',
                'direccion' => 'Calle Los Pinos 789, José Leonardo Ortiz',
                'licencia_conducir' => 'A-IIB-34567890',
                'fecha_vencimiento_licencia' => '2025-03-15',
                'funcion_id' => $funcionConductor->id,
                'clave' => '1234',
                'activo' => true,
            ],
            [
                'dni' => '45678901',
                'nombres' => 'Roberto Carlos',
                'apellido_paterno' => 'Mendoza',
                'apellido_materno' => 'Vargas',
                'fecha_nacimiento' => '1992-05-18',
                'telefono' => '966554433',
                'email' => 'roberto.mendoza@jlo.gob.pe',
                'direccion' => 'Av. Chiclayo 234, José Leonardo Ortiz',
                'licencia_conducir' => 'A-IIB-45678901',
                'fecha_vencimiento_licencia' => '2028-09-20',
                'funcion_id' => $funcionConductor->id,
                'clave' => '1234',
                'activo' => true,
            ],

            // Ayudantes
            [
                'dni' => '56789012',
                'nombres' => 'Juan Carlos',
                'apellido_paterno' => 'López',
                'apellido_materno' => 'Ramírez',
                'fecha_nacimiento' => '1995-02-28',
                'telefono' => '977665544',
                'email' => 'juan.lopez@jlo.gob.pe',
                'direccion' => 'Jr. La Victoria 567, José Leonardo Ortiz',
                'licencia_conducir' => null,
                'fecha_vencimiento_licencia' => null,
                'funcion_id' => $funcionAyudante->id,
                'clave' => '1234',
                'activo' => true,
            ],
            [
                'dni' => '67890123',
                'nombres' => 'Pedro Antonio',
                'apellido_paterno' => 'Sánchez',
                'apellido_materno' => 'Gutiérrez',
                'fecha_nacimiento' => '1993-08-14',
                'telefono' => '988776655',
                'email' => 'pedro.sanchez@jlo.gob.pe',
                'direccion' => 'Calle Real 890, José Leonardo Ortiz',
                'licencia_conducir' => null,
                'fecha_vencimiento_licencia' => null,
                'funcion_id' => $funcionAyudante->id,
                'clave' => '1234',
                'activo' => true,
            ],
            [
                'dni' => '78901234',
                'nombres' => 'Luis Alberto',
                'apellido_paterno' => 'Castro',
                'apellido_materno' => 'Morales',
                'fecha_nacimiento' => '1996-12-05',
                'telefono' => '999887766',
                'email' => 'luis.castro@jlo.gob.pe',
                'direccion' => 'Av. Bolognesi 123, José Leonardo Ortiz',
                'licencia_conducir' => null,
                'fecha_vencimiento_licencia' => null,
                'funcion_id' => $funcionAyudante->id,
                'clave' => '1234',
                'activo' => true,
            ],
            [
                'dni' => '89012345',
                'nombres' => 'Mario Enrique',
                'apellido_paterno' => 'Vega',
                'apellido_materno' => 'Ríos',
                'fecha_nacimiento' => '1994-04-25',
                'telefono' => '955667788',
                'email' => 'mario.vega@jlo.gob.pe',
                'direccion' => 'Jr. Tacna 456, José Leonardo Ortiz',
                'licencia_conducir' => null,
                'fecha_vencimiento_licencia' => null,
                'funcion_id' => $funcionAyudante->id,
                'clave' => '1234',
                'activo' => true,
            ],

            // Supervisor
            [
                'dni' => '90123456',
                'nombres' => 'Ricardo Manuel',
                'apellido_paterno' => 'Díaz',
                'apellido_materno' => 'Flores',
                'fecha_nacimiento' => '1980-09-30',
                'telefono' => '944556677',
                'email' => 'ricardo.diaz@jlo.gob.pe',
                'direccion' => 'Av. Grau 789, José Leonardo Ortiz',
                'licencia_conducir' => 'A-IIB-90123456',
                'fecha_vencimiento_licencia' => '2027-11-15',
                'funcion_id' => $funcionSupervisor->id,
                'clave' => '1234',
                'activo' => true,
            ],
        ];

        foreach ($personal as $persona) {
            Personal::create($persona);
        }
    }
}

