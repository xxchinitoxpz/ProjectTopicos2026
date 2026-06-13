<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ZoneSeeder extends Seeder
{
    public function run(): void
    {
        $zones = [
            [
                'name'         => 'Zona Centro Histórico',
                'district_id'  => 1,  // Chiclayo
                'description'  => 'Comprende el centro histórico de Chiclayo, mercado central y zonas aledañas.',
                'avg_waste_kg' => 320.50,
                'status'       => 'active',
                'area'         => 0.312000,
                'coords' => [
                    [-6.7714, -79.8409],
                    [-6.7689, -79.8409],
                    [-6.7689, -79.8370],
                    [-6.7714, -79.8370],
                    [-6.7730, -79.8390],
                ],
            ],
            [
                'name'         => 'Zona Norte JLO',
                'district_id'  => 2,  // José Leonardo Ortiz
                'description'  => 'Sector norte del distrito de José Leonardo Ortiz, zona residencial y comercial.',
                'avg_waste_kg' => 180.00,
                'status'       => 'active',
                'area'         => 0.485000,
                'coords' => [
                    [-6.7580, -79.8470],
                    [-6.7550, -79.8440],
                    [-6.7525, -79.8460],
                    [-6.7540, -79.8510],
                    [-6.7570, -79.8520],
                    [-6.7600, -79.8495],
                ],
            ],
            [
                'name'         => 'Zona La Victoria Sur',
                'district_id'  => 3,  // La Victoria
                'description'  => 'Parte sur del distrito La Victoria, incluye zonas industriales y residenciales.',
                'avg_waste_kg' => 210.75,
                'status'       => 'active',
                'area'         => 0.390000,
                'coords' => [
                    [-6.7800, -79.8450],
                    [-6.7775, -79.8420],
                    [-6.7755, -79.8445],
                    [-6.7760, -79.8490],
                    [-6.7790, -79.8500],
                    [-6.7815, -79.8480],
                ],
            ],
            [
                'name'         => 'Zona Pimentel Playa',
                'district_id'  => 4,  // Pimentel
                'description'  => 'Franja costera de Pimentel, zona turística y residencial frente al mar.',
                'avg_waste_kg' => 95.00,
                'status'       => 'active',
                'area'         => 0.228000,
                'coords' => [
                    [-6.8360, -79.9320],
                    [-6.8330, -79.9290],
                    [-6.8310, -79.9310],
                    [-6.8320, -79.9360],
                    [-6.8350, -79.9370],
                    [-6.8375, -79.9350],
                ],
            ],
            [
                'name'         => 'Zona Monsefú Central',
                'district_id'  => 6,  // Monsefú
                'description'  => 'Centro del distrito de Monsefú, zona artesanal y mercado de flores.',
                'avg_waste_kg' => 75.25,
                'status'       => 'active',
                'area'         => 0.195000,
                'coords' => [
                    [-6.8742, -79.8817],
                    [-6.8715, -79.8790],
                    [-6.8700, -79.8815],
                    [-6.8710, -79.8850],
                    [-6.8740, -79.8860],
                    [-6.8760, -79.8840],
                ],
            ],
            [
                'name'         => 'Zona Chiclayo Este',
                'district_id'  => 1,  // Chiclayo
                'description'  => 'Sector este de Chiclayo, urbanizaciones residenciales y hospitales.',
                'avg_waste_kg' => 145.00,
                'status'       => 'inactive',
                'area'         => 0.275000,
                'coords' => [
                    [-6.7690, -79.8340],
                    [-6.7660, -79.8310],
                    [-6.7640, -79.8340],
                    [-6.7650, -79.8380],
                    [-6.7680, -79.8390],
                    [-6.7705, -79.8368],
                ],
            ],
        ];

        foreach ($zones as $zoneData) {
            $coords = $zoneData['coords'];
            unset($zoneData['coords']);

            $zoneId = DB::table('zones')->insertGetId(array_merge($zoneData, [
                'sector_id'  => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]));

            foreach ($coords as $coord) {
                DB::table('zone_coords')->insert([
                    'zone_id'    => $zoneId,
                    'latitude'   => $coord[0],
                    'longitude'  => $coord[1],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
