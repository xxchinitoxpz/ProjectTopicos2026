<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            ['name' => 'Toyota',    'description' => 'Marca japonesa de vehículos'],
            ['name' => 'Mercedes-Benz', 'description' => 'Marca alemana de vehículos de alta gama'],
            ['name' => 'Volvo',     'description' => 'Marca sueca especializada en vehículos pesados'],
            ['name' => 'Hino',      'description' => 'Marca japonesa de camiones y buses'],
            ['name' => 'Hyundai',   'description' => 'Marca coreana de vehículos'],
        ];

        foreach ($brands as $brand) {
            DB::table('brands')->insert(array_merge($brand, [
                'logo'       => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
