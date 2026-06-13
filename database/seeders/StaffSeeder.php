<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StaffSeeder extends Seeder
{
    public function run(): void
    {
        $members = [
            [
                'dni'           => '45678901',
                'name'          => 'Carlos',
                'last_name'     => 'Mendoza Ríos',
                'email'         => 'carlos.mendoza@rsu.pe',
                'birthdate'     => '1985-03-15',
                'phone'         => '944111222',
                'address'       => 'Av. Los Pinos 123, Chiclayo',
                'vacation_days' => 30,
                'staff_type_id' => 2, // Conductor
                'status'        => 'active',
            ],
            [
                'dni'           => '46789012',
                'name'          => 'María',
                'last_name'     => 'Torres Salcedo',
                'email'         => 'maria.torres@rsu.pe',
                'birthdate'     => '1990-07-22',
                'phone'         => '944222333',
                'address'       => 'Jr. Las Flores 456, Chiclayo',
                'vacation_days' => 30,
                'staff_type_id' => 1, // Operario
                'status'        => 'active',
            ],
            [
                'dni'           => '47890123',
                'name'          => 'Juan',
                'last_name'     => 'Pérez Vásquez',
                'email'         => 'juan.perez@rsu.pe',
                'birthdate'     => '1988-11-05',
                'phone'         => '944333444',
                'address'       => 'Calle San Martín 789, Chiclayo',
                'vacation_days' => 30,
                'staff_type_id' => 1, // Operario
                'status'        => 'active',
            ],
            [
                'dni'           => '48901234',
                'name'          => 'Ana',
                'last_name'     => 'Llontop García',
                'email'         => 'ana.llontop@rsu.pe',
                'birthdate'     => '1992-04-18',
                'phone'         => '944444555',
                'address'       => 'Urb. Santa Victoria 321, Chiclayo',
                'vacation_days' => 30,
                'staff_type_id' => 3, // Supervisor
                'status'        => 'active',
            ],
            [
                'dni'           => '49012345',
                'name'          => 'Roberto',
                'last_name'     => 'Chafloque Núñez',
                'email'         => 'roberto.chafloque@rsu.pe',
                'birthdate'     => '1983-09-30',
                'phone'         => '944555666',
                'address'       => 'Av. Balta 654, Chiclayo',
                'vacation_days' => 30,
                'staff_type_id' => 4, // Técnico
                'status'        => 'active',
            ],
            [
                'dni'           => '50123456',
                'name'          => 'Lucía',
                'last_name'     => 'Fernández Odar',
                'email'         => 'lucia.fernandez@rsu.pe',
                'birthdate'     => '1995-12-10',
                'phone'         => '944666777',
                'address'       => 'Jr. Elías Aguirre 987, Chiclayo',
                'vacation_days' => 30,
                'staff_type_id' => 5, // Administrativo
                'status'        => 'active',
            ],
            [
                'dni'           => '51234567',
                'name'          => 'Pedro',
                'last_name'     => 'Sánchez Idrogo',
                'email'         => 'pedro.sanchez@rsu.pe',
                'birthdate'     => '1979-06-25',
                'phone'         => '944777888',
                'address'       => 'Av. Grau 147, Chiclayo',
                'vacation_days' => 15,
                'staff_type_id' => 2, // Conductor
                'status'        => 'inactive',
            ],
        ];

        foreach ($members as $member) {
            DB::table('staff')->insert(array_merge($member, [
                'photo'      => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
