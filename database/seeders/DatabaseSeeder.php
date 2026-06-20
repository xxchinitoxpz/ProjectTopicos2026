<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Usuario administrador de prueba
        User::factory()->create([
            'name'  => 'Administrador',
            'email' => 'admin@rsu.pe',
        ]);

        // Geografía
        $this->call([GeoSeeder::class]);

        // Catálogos de vehículos
        $this->call([
            VehicleColorSeeder::class,
            VehicleTypeSeeder::class,
            BrandSeeder::class,
            BrandModelSeeder::class,
            VehicleSeeder::class,
        ]);

        // Zonas de prueba
        $this->call([ZoneSeeder::class]);

        // Personal
        $this->call([
            StaffTypeSeeder::class,
            StaffSeeder::class,
            ShiftSeeder::class,
            StaffGroupSeeder::class,
            ContractSeeder::class,
            VacationSeeder::class,
            AssistanceSeeder::class,
        ]);
    }
}
