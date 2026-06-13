<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AssistanceSeeder extends Seeder
{
    public function run(): void
    {
        $records = [
            // Carlos (staff_id=1) - turno mañana
            ['staff_id' => 1, 'date_time' => '2026-06-09 06:05:00', 'type' => 'ingreso', 'state' => 'presente', 'shift_id' => 1, 'notes' => null],
            ['staff_id' => 1, 'date_time' => '2026-06-09 14:02:00', 'type' => 'salida',  'state' => 'presente', 'shift_id' => 1, 'notes' => null],
            ['staff_id' => 1, 'date_time' => '2026-06-08 06:10:00', 'type' => 'ingreso', 'state' => 'presente', 'shift_id' => 1, 'notes' => null],
            ['staff_id' => 1, 'date_time' => '2026-06-08 14:00:00', 'type' => 'salida',  'state' => 'presente', 'shift_id' => 1, 'notes' => null],

            // María (staff_id=2) - turno mañana
            ['staff_id' => 2, 'date_time' => '2026-06-09 06:15:00', 'type' => 'ingreso', 'state' => 'presente', 'shift_id' => 1, 'notes' => 'Llegó 15 min tarde'],
            ['staff_id' => 2, 'date_time' => '2026-06-09 14:00:00', 'type' => 'salida',  'state' => 'presente', 'shift_id' => 1, 'notes' => null],
            ['staff_id' => 2, 'date_time' => '2026-06-08 00:00:00', 'type' => 'ingreso', 'state' => 'ausente',  'shift_id' => 1, 'notes' => 'Falta injustificada'],

            // Juan (staff_id=3) - turno tarde
            ['staff_id' => 3, 'date_time' => '2026-06-09 14:05:00', 'type' => 'ingreso', 'state' => 'presente', 'shift_id' => 2, 'notes' => null],
            ['staff_id' => 3, 'date_time' => '2026-06-09 22:00:00', 'type' => 'salida',  'state' => 'presente', 'shift_id' => 2, 'notes' => null],

            // Ana (staff_id=4) - turno mañana
            ['staff_id' => 4, 'date_time' => '2026-06-09 06:00:00', 'type' => 'ingreso', 'state' => 'presente', 'shift_id' => 1, 'notes' => null],
            ['staff_id' => 4, 'date_time' => '2026-06-09 14:00:00', 'type' => 'salida',  'state' => 'presente', 'shift_id' => 1, 'notes' => null],

            // Roberto (staff_id=5) - turno tarde
            ['staff_id' => 5, 'date_time' => '2026-06-09 14:00:00', 'type' => 'ingreso', 'state' => 'presente', 'shift_id' => 2, 'notes' => null],
            ['staff_id' => 5, 'date_time' => '2026-06-08 14:00:00', 'type' => 'ingreso', 'state' => 'presente', 'shift_id' => 2, 'notes' => null],
            ['staff_id' => 5, 'date_time' => '2026-06-08 22:05:00', 'type' => 'salida',  'state' => 'presente', 'shift_id' => 2, 'notes' => null],

            // Lucía (staff_id=6) - turno mañana
            ['staff_id' => 6, 'date_time' => '2026-06-09 06:00:00', 'type' => 'ingreso', 'state' => 'presente', 'shift_id' => 1, 'notes' => null],
            ['staff_id' => 6, 'date_time' => '2026-06-09 14:00:00', 'type' => 'salida',  'state' => 'presente', 'shift_id' => 1, 'notes' => null],
        ];

        foreach ($records as $record) {
            DB::table('assistances')->insert(array_merge($record, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
