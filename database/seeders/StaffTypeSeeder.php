<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StaffTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'Operario de Limpieza',  'description' => 'Personal encargado de la recolección directa de residuos'],
            ['name' => 'Conductor',              'description' => 'Personal habilitado para conducir vehículos de recolección'],
            ['name' => 'Supervisor de Campo',    'description' => 'Encargado de supervisar las rutas y el personal en campo'],
            ['name' => 'Técnico de Mantenimiento', 'description' => 'Encargado del mantenimiento preventivo y correctivo de vehículos'],
            ['name' => 'Administrativo',         'description' => 'Personal de oficina y gestión administrativa'],
        ];

        foreach ($types as $type) {
            DB::table('staff_types')->insert(array_merge($type, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
