<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DemoWorkSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        $this->truncateWorkTables();
        $this->seedCatalogs();
        $this->seedGeoAndZones();
        $this->seedStaff();
        $this->seedGroups();
        $this->seedHolidays();
        $this->seedSamplePlanning();

        Schema::enableForeignKeyConstraints();
    }

    private function truncateWorkTables(): void
    {
        $tables = [
            'planning_changes',
            'planning_day_helpers',
            'planning_days',
            'plannings',
            'staff_group_helpers',
            'staff_groups',
            'holidays',
            'assistances',
            'vacations',
            'contracts',
            'staff',
            'staff_types',
            'shifts',
            'zone_coords',
            'zones',
            'sectors',
            'districts',
            'provinces',
            'departments',
            'vehicle_routes',
            'route_paths',
            'route_zone',
            'routes',
            'vehicle_images',
            'vehicles',
            'brand_models',
            'brands',
            'vehicle_types',
            'vehicle_colors',
            'schedules',
            'user_types',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->truncate();
            }
        }
    }

    private function seedCatalogs(): void
    {
        $now = now();

        foreach ([
            ['name' => 'Blanco', 'code' => '#FFFFFF', 'description' => 'Color blanco'],
            ['name' => 'Verde', 'code' => '#008000', 'description' => 'Color verde'],
            ['name' => 'Amarillo', 'code' => '#FFFF00', 'description' => 'Color amarillo'],
        ] as $color) {
            DB::table('vehicle_colors')->insert(array_merge($color, ['created_at' => $now, 'updated_at' => $now]));
        }

        foreach ([
            ['name' => 'Camión Recolector', 'description' => 'Recolección de residuos'],
            ['name' => 'Motokar', 'description' => 'Zonas de difícil acceso'],
            ['name' => 'Compactadora', 'description' => 'Compactación de residuos'],
        ] as $type) {
            DB::table('vehicle_types')->insert(array_merge($type, ['created_at' => $now, 'updated_at' => $now]));
        }

        foreach ([
            ['name' => 'Hino', 'description' => 'Camiones japoneses', 'logo' => null],
            ['name' => 'Toyota', 'description' => 'Vehículos Toyota', 'logo' => null],
            ['name' => 'Hyundai', 'description' => 'Vehículos Hyundai', 'logo' => null],
        ] as $brand) {
            DB::table('brands')->insert(array_merge($brand, ['created_at' => $now, 'updated_at' => $now]));
        }

        DB::table('brand_models')->insert([
            ['brand_id' => 1, 'name' => 'FC', 'description' => 'Camión recolector', 'created_at' => $now, 'updated_at' => $now],
            ['brand_id' => 2, 'name' => 'Hilux', 'description' => 'Camioneta apoyo', 'created_at' => $now, 'updated_at' => $now],
            ['brand_id' => 3, 'name' => 'Mighty', 'description' => 'Camión ligero', 'created_at' => $now, 'updated_at' => $now],
        ]);

        DB::table('vehicles')->insert([
            [
                'name' => 'Recolector Norte 01', 'code' => 'VH-001', 'plate' => 'ABC-123', 'year' => 2020,
                'occupant_capacity' => 3, 'load_capacity' => 8000, 'combustible_capacity' => 200,
                'compaction_capacity' => 6000, 'description' => 'Ruta norte', 'status' => 'active',
                'brand_id' => 1, 'model_id' => 1, 'type_id' => 1, 'color_id' => 1,
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'name' => 'Recolector Sur 02', 'code' => 'VH-002', 'plate' => 'DEF-456', 'year' => 2019,
                'occupant_capacity' => 3, 'load_capacity' => 7500, 'combustible_capacity' => 180,
                'compaction_capacity' => 5500, 'description' => 'Ruta sur', 'status' => 'active',
                'brand_id' => 1, 'model_id' => 1, 'type_id' => 3, 'color_id' => 2,
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'name' => 'Motokar Centro 01', 'code' => 'VH-003', 'plate' => 'GHI-789', 'year' => 2022,
                'occupant_capacity' => 2, 'load_capacity' => 500, 'combustible_capacity' => 10,
                'compaction_capacity' => null, 'description' => 'Centro histórico', 'status' => 'active',
                'brand_id' => 3, 'model_id' => 3, 'type_id' => 2, 'color_id' => 3,
                'created_at' => $now, 'updated_at' => $now,
            ],
        ]);

        DB::table('user_types')->insert([
            ['name' => 'Administrador', 'description' => 'Acceso total al sistema', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Supervisor', 'description' => 'Supervisión operativa', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Operador', 'description' => 'Operación diaria', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    private function seedGeoAndZones(): void
    {
        $now = now();

        DB::table('departments')->insert([
            ['id' => 1, 'name' => 'Lambayeque', 'code' => '14', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'name' => 'Lima', 'code' => '15', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'name' => 'La Libertad', 'code' => '13', 'created_at' => $now, 'updated_at' => $now],
        ]);

        DB::table('provinces')->insert([
            ['id' => 1, 'department_id' => 1, 'name' => 'Chiclayo', 'code' => '1401', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'department_id' => 1, 'name' => 'Lambayeque', 'code' => '1402', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'department_id' => 2, 'name' => 'Lima', 'code' => '1501', 'created_at' => $now, 'updated_at' => $now],
        ]);

        DB::table('districts')->insert([
            ['id' => 1, 'province_id' => 1, 'department_id' => 1, 'name' => 'Chiclayo', 'code' => '140101', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'province_id' => 1, 'department_id' => 1, 'name' => 'José Leonardo Ortiz', 'code' => '140102', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'province_id' => 1, 'department_id' => 1, 'name' => 'La Victoria', 'code' => '140103', 'created_at' => $now, 'updated_at' => $now],
        ]);

        $zones = [
            [
                'name' => 'Zona Centro Histórico', 'district_id' => 1,
                'description' => 'Centro de Chiclayo', 'avg_waste_kg' => 320.5,
                'status' => 'active', 'area' => 0.312,
                'coords' => [[-6.7714, -79.8409], [-6.7689, -79.8370], [-6.7714, -79.8370]],
            ],
            [
                'name' => 'Zona Norte JLO', 'district_id' => 2,
                'description' => 'José Leonardo Ortiz norte', 'avg_waste_kg' => 180,
                'status' => 'active', 'area' => 0.485,
                'coords' => [[-6.7580, -79.8470], [-6.7525, -79.8460], [-6.7540, -79.8510]],
            ],
            [
                'name' => 'Zona La Victoria Sur', 'district_id' => 3,
                'description' => 'La Victoria sur', 'avg_waste_kg' => 210.75,
                'status' => 'active', 'area' => 0.39,
                'coords' => [[-6.7800, -79.8450], [-6.7755, -79.8445], [-6.7760, -79.8490]],
            ],
        ];

        foreach ($zones as $zoneData) {
            $coords = $zoneData['coords'];
            unset($zoneData['coords']);

            if (Schema::hasColumn('zones', 'avg_waste')) {
                $zoneData['avg_waste'] = $zoneData['avg_waste_kg'];
            }
            unset($zoneData['avg_waste_kg']);

            $zoneId = DB::table('zones')->insertGetId(array_merge($zoneData, [
                'sector_id' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]));

            foreach ($coords as $coord) {
                DB::table('zone_coords')->insert([
                    'zone_id' => $zoneId,
                    'latitude' => $coord[0],
                    'longitude' => $coord[1],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    private function seedStaff(): void
    {
        $now = now();

        DB::table('staff_types')->insert([
            ['name' => 'Operario de Limpieza', 'description' => 'Recolección en campo', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Conductor', 'description' => 'Conduce vehículos de recolección', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Supervisor de Campo', 'description' => 'Supervisa rutas', 'created_at' => $now, 'updated_at' => $now],
        ]);

        DB::table('shifts')->insert([
            ['name' => 'Turno Mañana', 'description' => '06:00 - 14:00', 'check_in' => '06:00:00', 'check_out' => '14:00:00', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Turno Tarde', 'description' => '14:00 - 22:00', 'check_in' => '14:00:00', 'check_out' => '22:00:00', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Turno Noche', 'description' => '22:00 - 06:00', 'check_in' => '22:00:00', 'check_out' => '06:00:00', 'created_at' => $now, 'updated_at' => $now],
        ]);

        DB::table('staff')->insert([
            [
                'dni' => '45678901', 'name' => 'Carlos', 'last_name' => 'Mendoza Ríos',
                'email' => 'carlos.mendoza@rsu.pe', 'birthdate' => '1985-03-15',
                'phone' => '944111222', 'address' => 'Av. Los Pinos 123, Chiclayo',
                'vacation_days' => 30, 'photo' => null, 'staff_type_id' => 2, 'status' => 'active',
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'dni' => '46789012', 'name' => 'María', 'last_name' => 'Torres Salcedo',
                'email' => 'maria.torres@rsu.pe', 'birthdate' => '1990-07-22',
                'phone' => '944222333', 'address' => 'Jr. Las Flores 456, Chiclayo',
                'vacation_days' => 30, 'photo' => null, 'staff_type_id' => 1, 'status' => 'active',
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'dni' => '47890123', 'name' => 'Pedro', 'last_name' => 'Sánchez Idrogo',
                'email' => 'pedro.sanchez@rsu.pe', 'birthdate' => '1979-06-25',
                'phone' => '944777888', 'address' => 'Av. Grau 147, Chiclayo',
                'vacation_days' => 30, 'photo' => null, 'staff_type_id' => 2, 'status' => 'active',
                'created_at' => $now, 'updated_at' => $now,
            ],
        ]);

        DB::table('contracts')->insert([
            ['staff_id' => 1, 'contract_type' => 'permanente', 'date_start' => '2020-01-01', 'date_end' => null, 'salary' => 2500, 'probation' => null, 'state' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['staff_id' => 2, 'contract_type' => 'nombrado', 'date_start' => '2021-03-01', 'date_end' => null, 'salary' => 1800, 'probation' => null, 'state' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['staff_id' => 3, 'contract_type' => 'permanente', 'date_start' => '2019-07-15', 'date_end' => null, 'salary' => 2200, 'probation' => null, 'state' => 'active', 'created_at' => $now, 'updated_at' => $now],
        ]);

        DB::table('vacations')->insert([
            ['staff_id' => 1, 'date_request' => '2026-05-01', 'date_start' => '2026-08-01', 'date_end' => '2026-08-05', 'days_requested' => 5, 'notes' => 'Vacaciones programadas', 'state' => 'approved', 'created_at' => $now, 'updated_at' => $now],
            ['staff_id' => 2, 'date_request' => '2026-06-01', 'date_start' => '2026-07-01', 'date_end' => '2026-07-03', 'days_requested' => 3, 'notes' => 'Viaje familiar', 'state' => 'pending', 'created_at' => $now, 'updated_at' => $now],
            ['staff_id' => 3, 'date_request' => '2026-04-10', 'date_start' => '2026-09-01', 'date_end' => '2026-09-03', 'days_requested' => 3, 'notes' => 'Descanso médico', 'state' => 'pending', 'created_at' => $now, 'updated_at' => $now],
        ]);

        DB::table('assistances')->insert([
            ['staff_id' => 1, 'date_time' => '2026-06-16 06:05:00', 'type' => 'ingreso', 'state' => 'presente', 'shift_id' => 1, 'notes' => null, 'created_at' => $now, 'updated_at' => $now],
            ['staff_id' => 2, 'date_time' => '2026-06-16 06:10:00', 'type' => 'ingreso', 'state' => 'presente', 'shift_id' => 1, 'notes' => null, 'created_at' => $now, 'updated_at' => $now],
            ['staff_id' => 3, 'date_time' => '2026-06-16 14:05:00', 'type' => 'ingreso', 'state' => 'presente', 'shift_id' => 2, 'notes' => null, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    private function seedGroups(): void
    {
        $now = now();

        $groups = [
            [
                'name' => 'Grupo Norte Chiclayo',
                'shift_id' => 1, 'zone_id' => 1, 'vehicle_id' => 1, 'driver_id' => 1,
                'days' => json_encode(['lunes', 'martes', 'miercoles', 'jueves', 'viernes']),
                'status' => 'active', 'helpers' => [2],
            ],
            [
                'name' => 'Grupo Sur JLO',
                'shift_id' => 2, 'zone_id' => 2, 'vehicle_id' => 2, 'driver_id' => 3,
                'days' => json_encode(['lunes', 'miercoles', 'viernes']),
                'status' => 'active', 'helpers' => [],
            ],
            [
                'name' => 'Grupo La Victoria',
                'shift_id' => 3, 'zone_id' => 3, 'vehicle_id' => 3, 'driver_id' => 1,
                'days' => json_encode(['martes', 'jueves', 'sabado']),
                'status' => 'active', 'helpers' => [2],
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

    private function seedHolidays(): void
    {
        $now = now();

        DB::table('holidays')->insert([
            ['date' => '2026-06-24', 'description' => 'San Juan y San Pedro', 'state' => 'activo', 'created_at' => $now, 'updated_at' => $now],
            ['date' => '2026-07-28', 'description' => 'Fiestas Patrias', 'state' => 'activo', 'created_at' => $now, 'updated_at' => $now],
            ['date' => '2026-07-29', 'description' => 'Fiestas Patrias', 'state' => 'activo', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    private function seedSamplePlanning(): void
    {
        $now = now();
        $userId = DB::table('users')->value('id') ?? 1;

        $planningId = DB::table('plannings')->insertGetId([
            'staff_group_id' => 1,
            'date_start' => '2026-06-16',
            'date_end' => '2026-06-20',
            'days' => json_encode(['lunes', 'martes', 'miercoles', 'jueves', 'viernes']),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $days = [
            ['date' => '2026-06-16', 'state' => 'active'],
            ['date' => '2026-06-17', 'state' => 'reprogramado'],
            ['date' => '2026-06-18', 'state' => 'active'],
        ];

        foreach ($days as $day) {
            $dayId = DB::table('planning_days')->insertGetId([
                'planning_id' => $planningId,
                'date' => $day['date'],
                'shift_id' => 1,
                'vehicle_id' => 1,
                'driver_id' => 1,
                'state' => $day['state'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            DB::table('planning_day_helpers')->insert([
                'planning_day_id' => $dayId,
                'staff_id' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            DB::table('planning_changes')->insert([
                'planning_day_id' => $dayId,
                'user_id' => $userId,
                'change_type' => 'creacion',
                'old_value' => 'N/A',
                'new_value' => 'Creado',
                'reason_type' => 'Programación Individual',
                'details' => 'Programación de demostración.',
                'created_at' => $now,
            ]);
        }
    }
}
