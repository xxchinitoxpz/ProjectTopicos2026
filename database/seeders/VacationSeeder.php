<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VacationSeeder extends Seeder
{
    public function run(): void
    {
        $vacations = [
            // Carlos Mendoza - aprobada
            [
                'staff_id'       => 1,
                'date_request'   => '2026-05-01',
                'date_start'     => '2026-06-01',
                'date_end'       => '2026-06-10',
                'days_requested' => 10,
                'notes'          => 'Vacaciones anuales programadas',
                'state'          => 'approved',
            ],
            // María Torres - pendiente
            [
                'staff_id'       => 2,
                'date_request'   => '2026-06-01',
                'date_start'     => '2026-07-01',
                'date_end'       => '2026-07-15',
                'days_requested' => 15,
                'notes'          => 'Viaje familiar',
                'state'          => 'pending',
            ],
            // Juan Pérez - rechazada
            [
                'staff_id'       => 3,
                'date_request'   => '2026-04-10',
                'date_start'     => '2026-04-20',
                'date_end'       => '2026-04-30',
                'days_requested' => 10,
                'notes'          => 'Solicitud rechazada por alta demanda operativa',
                'state'          => 'rejected',
            ],
            // Ana Llontop - pendiente
            [
                'staff_id'       => 4,
                'date_request'   => '2026-06-05',
                'date_start'     => '2026-08-01',
                'date_end'       => '2026-08-07',
                'days_requested' => 7,
                'notes'          => null,
                'state'          => 'pending',
            ],
        ];

        foreach ($vacations as $vacation) {
            DB::table('vacations')->insert(array_merge($vacation, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
