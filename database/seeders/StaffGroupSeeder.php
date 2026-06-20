<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StaffGroupSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $groups = [
            [
                'name' => 'Grupo Norte Chiclayo',
                'shift_id' => 1,
                'zone_id' => 1,
                'vehicle_id' => 1,
                'driver_id' => 1,
                'days' => json_encode(['lunes', 'martes', 'miercoles', 'jueves', 'viernes']),
                'status' => 'active',
                'helpers' => [2, 3],
            ],
            [
                'name' => 'Grupo Sur JLO',
                'shift_id' => 2,
                'zone_id' => 2,
                'vehicle_id' => 2,
                'driver_id' => 4,
                'days' => json_encode(['lunes', 'miercoles', 'viernes']),
                'status' => 'active',
                'helpers' => [5],
            ],
            [
                'name' => 'Grupo La Victoria',
                'shift_id' => 3,
                'zone_id' => 3,
                'vehicle_id' => 3,
                'driver_id' => 6,
                'days' => json_encode(['martes', 'jueves', 'sabado']),
                'status' => 'active',
                'helpers' => [7],
            ],
        ];

        foreach ($groups as $group) {
            $helpers = $group['helpers'];
            unset($group['helpers']);

            $groupId = DB::table('staff_groups')->insertGetId(array_merge($group, [
                'created_at' => $now,
                'updated_at' => $now,
            ]));

            foreach ($helpers as $helperId) {
                DB::table('staff_group_helpers')->insert([
                    'staff_group_id' => $groupId,
                    'staff_id' => $helperId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }
}
