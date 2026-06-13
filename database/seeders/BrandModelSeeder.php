<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BrandModelSeeder extends Seeder
{
    public function run(): void
    {
        $models = [
            // Toyota (brand_id = 1)
            ['brand_id' => 1, 'name' => 'Hilux',        'description' => 'Camioneta doble cabina'],
            ['brand_id' => 1, 'name' => 'Land Cruiser',  'description' => 'SUV todo terreno'],
            // Mercedes-Benz (brand_id = 2)
            ['brand_id' => 2, 'name' => 'Actros',        'description' => 'Camión de carga pesada'],
            ['brand_id' => 2, 'name' => 'Atego',         'description' => 'Camión mediano de distribución'],
            // Volvo (brand_id = 3)
            ['brand_id' => 3, 'name' => 'FH16',          'description' => 'Camión de alto tonelaje'],
            ['brand_id' => 3, 'name' => 'FM',            'description' => 'Camión versátil de carga'],
            // Hino (brand_id = 4)
            ['brand_id' => 4, 'name' => 'FC',            'description' => 'Camión mediano recolector'],
            ['brand_id' => 4, 'name' => 'GH',            'description' => 'Camión pesado de residuos'],
            // Hyundai (brand_id = 5)
            ['brand_id' => 5, 'name' => 'HD78',          'description' => 'Camión mediano multifunción'],
            ['brand_id' => 5, 'name' => 'Mighty',        'description' => 'Camión ligero de carga'],
        ];

        foreach ($models as $model) {
            DB::table('brand_models')->insert(array_merge($model, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
