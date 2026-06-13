<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VehicleColorSeeder extends Seeder
{
    public function run(): void
    {
        $colors = [
            ['name' => 'Blanco',     'code' => '#FFFFFF', 'description' => 'Color blanco estándar'],
            ['name' => 'Negro',      'code' => '#000000', 'description' => 'Color negro'],
            ['name' => 'Gris',       'code' => '#808080', 'description' => 'Color gris medio'],
            ['name' => 'Rojo',       'code' => '#FF0000', 'description' => 'Color rojo'],
            ['name' => 'Azul',       'code' => '#0000FF', 'description' => 'Color azul'],
            ['name' => 'Verde',      'code' => '#008000', 'description' => 'Color verde'],
            ['name' => 'Amarillo',   'code' => '#FFFF00', 'description' => 'Color amarillo'],
            ['name' => 'Naranja',    'code' => '#FFA500', 'description' => 'Color naranja'],
        ];

        foreach ($colors as $color) {
            DB::table('vehicle_colors')->insert(array_merge($color, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
