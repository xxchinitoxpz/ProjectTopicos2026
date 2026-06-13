<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShiftSeeder extends Seeder
{
    public function run(): void
    {
        $shifts = [
            [
                'name'        => 'Turno Mañana',
                'description' => 'Turno de recolección matutina',
                'check_in'    => '06:00:00',
                'check_out'   => '14:00:00',
            ],
            [
                'name'        => 'Turno Tarde',
                'description' => 'Turno de recolección vespertina',
                'check_in'    => '14:00:00',
                'check_out'   => '22:00:00',
            ],
            [
                'name'        => 'Turno Noche',
                'description' => 'Turno de recolección nocturna',
                'check_in'    => '22:00:00',
                'check_out'   => '06:00:00',
            ],
        ];

        foreach ($shifts as $shift) {
            DB::table('shifts')->insert(array_merge($shift, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
