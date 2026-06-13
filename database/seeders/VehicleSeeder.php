<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VehicleSeeder extends Seeder
{
    public function run(): void
    {
        $vehicles = [
            [
                'name'                 => 'Recolector Norte 01',
                'code'                 => 'VH-001',
                'plate'                => 'ABC-123',
                'year'                 => 2020,
                'occupant_capacity'    => 3,
                'load_capacity'        => 8000.00,
                'combustible_capacity' => 200.00,
                'compaction_capacity'  => 6000.00,
                'description'          => 'Camión recolector asignado a la ruta norte',
                'status'               => 'active',
                'brand_id'             => 4, // Hino
                'model_id'             => 8, // GH
                'type_id'              => 3, // Compactadora
                'color_id'             => 1, // Blanco
            ],
            [
                'name'                 => 'Recolector Sur 02',
                'code'                 => 'VH-002',
                'plate'                => 'DEF-456',
                'year'                 => 2019,
                'occupant_capacity'    => 3,
                'load_capacity'        => 7500.00,
                'combustible_capacity' => 180.00,
                'compaction_capacity'  => 5500.00,
                'description'          => 'Camión recolector asignado a la ruta sur',
                'status'               => 'active',
                'brand_id'             => 4, // Hino
                'model_id'             => 7, // FC
                'type_id'              => 1, // Camión Recolector
                'color_id'             => 6, // Verde
            ],
            [
                'name'                 => 'Motokar Centro 01',
                'code'                 => 'VH-003',
                'plate'                => 'GHI-789',
                'year'                 => 2022,
                'occupant_capacity'    => 2,
                'load_capacity'        => 500.00,
                'combustible_capacity' => 10.00,
                'compaction_capacity'  => null,
                'description'          => 'Motokar para zonas de difícil acceso en el centro',
                'status'               => 'active',
                'brand_id'             => 5, // Hyundai
                'model_id'             => 10, // Mighty
                'type_id'              => 2, // Motokar
                'color_id'             => 7, // Amarillo
            ],
            [
                'name'                 => 'Volquete Residuos 01',
                'code'                 => 'VH-004',
                'plate'                => 'JKL-012',
                'year'                 => 2018,
                'occupant_capacity'    => 2,
                'load_capacity'        => 15000.00,
                'combustible_capacity' => 350.00,
                'compaction_capacity'  => null,
                'description'          => 'Volquete para transporte de residuos al relleno sanitario',
                'status'               => 'active',
                'brand_id'             => 3, // Volvo
                'model_id'             => 5, // FH16
                'type_id'              => 4, // Volquete
                'color_id'             => 8, // Naranja
            ],
            [
                'name'                 => 'Camioneta Supervisión',
                'code'                 => 'VH-005',
                'plate'                => 'MNO-345',
                'year'                 => 2021,
                'occupant_capacity'    => 5,
                'load_capacity'        => 1000.00,
                'combustible_capacity' => 60.00,
                'compaction_capacity'  => null,
                'description'          => 'Camioneta para supervisión y apoyo en campo',
                'status'               => 'active',
                'brand_id'             => 1, // Toyota
                'model_id'             => 1, // Hilux
                'type_id'              => 5, // Camioneta de Apoyo
                'color_id'             => 2, // Negro
            ],
            [
                'name'                 => 'Recolector Este 03',
                'code'                 => 'VH-006',
                'plate'                => 'PQR-678',
                'year'                 => 2017,
                'occupant_capacity'    => 3,
                'load_capacity'        => 7000.00,
                'combustible_capacity' => 160.00,
                'compaction_capacity'  => 5000.00,
                'description'          => 'Vehículo en mantenimiento preventivo',
                'status'               => 'inactive',
                'brand_id'             => 2, // Mercedes-Benz
                'model_id'             => 4, // Atego
                'type_id'              => 1, // Camión Recolector
                'color_id'             => 3, // Gris
            ],
        ];

        foreach ($vehicles as $vehicle) {
            DB::table('vehicles')->insert(array_merge($vehicle, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
