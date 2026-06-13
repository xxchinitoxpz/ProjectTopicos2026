<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeoSeeder extends Seeder
{
    public function run(): void
    {
        // ── Departamentos ──────────────────────────────────────────────
        $departments = [
            ['id' => 1,  'name' => 'Lambayeque',   'code' => '14'],
            ['id' => 2,  'name' => 'Lima',          'code' => '15'],
            ['id' => 3,  'name' => 'La Libertad',   'code' => '13'],
            ['id' => 4,  'name' => 'Piura',         'code' => '20'],
            ['id' => 5,  'name' => 'Cajamarca',     'code' => '06'],
        ];

        foreach ($departments as $d) {
            DB::table('departments')->insert(array_merge($d, ['created_at' => now(), 'updated_at' => now()]));
        }

        // ── Provincias ─────────────────────────────────────────────────
        $provinces = [
            // Lambayeque
            ['id' => 1,  'department_id' => 1, 'name' => 'Chiclayo',   'code' => '1401'],
            ['id' => 2,  'department_id' => 1, 'name' => 'Lambayeque', 'code' => '1402'],
            ['id' => 3,  'department_id' => 1, 'name' => 'Ferreñafe',  'code' => '1403'],
            // Lima
            ['id' => 4,  'department_id' => 2, 'name' => 'Lima',       'code' => '1501'],
            ['id' => 5,  'department_id' => 2, 'name' => 'Callao',     'code' => '1502'],
            // La Libertad
            ['id' => 6,  'department_id' => 3, 'name' => 'Trujillo',   'code' => '1301'],
            ['id' => 7,  'department_id' => 3, 'name' => 'Ascope',     'code' => '1302'],
            // Piura
            ['id' => 8,  'department_id' => 4, 'name' => 'Piura',      'code' => '2001'],
            // Cajamarca
            ['id' => 9,  'department_id' => 5, 'name' => 'Cajamarca',  'code' => '0601'],
        ];

        foreach ($provinces as $p) {
            DB::table('provinces')->insert(array_merge($p, ['created_at' => now(), 'updated_at' => now()]));
        }

        // ── Distritos ──────────────────────────────────────────────────
        $districts = [
            // Chiclayo (province_id=1)
            ['id' => 1,  'province_id' => 1, 'department_id' => 1, 'name' => 'Chiclayo',               'code' => '140101'],
            ['id' => 2,  'province_id' => 1, 'department_id' => 1, 'name' => 'José Leonardo Ortiz',    'code' => '140102'],
            ['id' => 3,  'province_id' => 1, 'department_id' => 1, 'name' => 'La Victoria',            'code' => '140103'],
            ['id' => 4,  'province_id' => 1, 'department_id' => 1, 'name' => 'Pimentel',               'code' => '140104'],
            ['id' => 5,  'province_id' => 1, 'department_id' => 1, 'name' => 'San José',               'code' => '140105'],
            ['id' => 6,  'province_id' => 1, 'department_id' => 1, 'name' => 'Monsefú',                'code' => '140106'],
            ['id' => 7,  'province_id' => 1, 'department_id' => 1, 'name' => 'Reque',                  'code' => '140107'],
            ['id' => 8,  'province_id' => 1, 'department_id' => 1, 'name' => 'Tumán',                  'code' => '140108'],
            ['id' => 9,  'province_id' => 1, 'department_id' => 1, 'name' => 'Pomalca',                'code' => '140109'],
            ['id' => 10, 'province_id' => 1, 'department_id' => 1, 'name' => 'Chongoyape',             'code' => '140110'],
            // Lambayeque (province_id=2)
            ['id' => 11, 'province_id' => 2, 'department_id' => 1, 'name' => 'Lambayeque',             'code' => '140201'],
            ['id' => 12, 'province_id' => 2, 'department_id' => 1, 'name' => 'Olmos',                  'code' => '140202'],
            ['id' => 13, 'province_id' => 2, 'department_id' => 1, 'name' => 'Motupe',                 'code' => '140203'],
            ['id' => 14, 'province_id' => 2, 'department_id' => 1, 'name' => 'Íllimo',                 'code' => '140204'],
            ['id' => 15, 'province_id' => 2, 'department_id' => 1, 'name' => 'Mórrope',                'code' => '140205'],
            // Ferreñafe (province_id=3)
            ['id' => 16, 'province_id' => 3, 'department_id' => 1, 'name' => 'Ferreñafe',              'code' => '140301'],
            ['id' => 17, 'province_id' => 3, 'department_id' => 1, 'name' => 'Pitipo',                 'code' => '140302'],
            ['id' => 18, 'province_id' => 3, 'department_id' => 1, 'name' => 'Pueblo Nuevo',           'code' => '140303'],
            ['id' => 19, 'province_id' => 3, 'department_id' => 1, 'name' => 'Incahuasi',              'code' => '140304'],
            ['id' => 20, 'province_id' => 3, 'department_id' => 1, 'name' => 'Cañaris',                'code' => '140305'],
            // Lima (province_id=4)
            ['id' => 21, 'province_id' => 4, 'department_id' => 2, 'name' => 'Lima',                   'code' => '150101'],
            ['id' => 22, 'province_id' => 4, 'department_id' => 2, 'name' => 'Miraflores',             'code' => '150102'],
            ['id' => 23, 'province_id' => 4, 'department_id' => 2, 'name' => 'San Isidro',             'code' => '150103'],
            // Trujillo (province_id=6)
            ['id' => 24, 'province_id' => 6, 'department_id' => 3, 'name' => 'Trujillo',               'code' => '130101'],
            ['id' => 25, 'province_id' => 6, 'department_id' => 3, 'name' => 'Victor Larco Herrera',   'code' => '130102'],
            // Piura (province_id=8)
            ['id' => 26, 'province_id' => 8, 'department_id' => 4, 'name' => 'Piura',                  'code' => '200101'],
            ['id' => 27, 'province_id' => 8, 'department_id' => 4, 'name' => 'Castilla',               'code' => '200102'],
        ];

        foreach ($districts as $dist) {
            DB::table('districts')->insert(array_merge($dist, ['created_at' => now(), 'updated_at' => now()]));
        }
    }
}
