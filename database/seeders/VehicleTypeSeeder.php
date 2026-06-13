<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VehicleTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'Camión Recolector',     'description' => 'Vehículo pesado para recolección de residuos sólidos'],
            ['name' => 'Motokar',               'description' => 'Vehículo menor de tres ruedas para zonas estrechas'],
            ['name' => 'Compactadora',          'description' => 'Camión con sistema de compactación de residuos'],
            ['name' => 'Volquete',              'description' => 'Vehículo para transporte de material a granel'],
            ['name' => 'Camioneta de Apoyo',    'description' => 'Vehículo liviano para supervisión y apoyo operativo'],
        ];

        foreach ($types as $type) {
            DB::table('vehicle_types')->insert(array_merge($type, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
