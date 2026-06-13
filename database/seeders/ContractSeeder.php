<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContractSeeder extends Seeder
{
    public function run(): void
    {
        $contracts = [
            // Carlos Mendoza (staff_id=1) - permanente activo
            [
                'staff_id'      => 1,
                'contract_type' => 'permanente',
                'date_start'    => '2020-01-01',
                'date_end'      => null,
                'salary'        => 2500.00,
                'probation'     => null,
                'state'         => 'active',
            ],
            // María Torres (staff_id=2) - nombrado activo
            [
                'staff_id'      => 2,
                'contract_type' => 'nombrado',
                'date_start'    => '2021-03-01',
                'date_end'      => null,
                'salary'        => 1800.00,
                'probation'     => null,
                'state'         => 'active',
            ],
            // Juan Pérez (staff_id=3) - permanente activo
            [
                'staff_id'      => 3,
                'contract_type' => 'permanente',
                'date_start'    => '2019-07-15',
                'date_end'      => null,
                'salary'        => 1800.00,
                'probation'     => null,
                'state'         => 'active',
            ],
            // Ana Llontop (staff_id=4) - nombrado activo
            [
                'staff_id'      => 4,
                'contract_type' => 'nombrado',
                'date_start'    => '2022-01-01',
                'date_end'      => null,
                'salary'        => 3200.00,
                'probation'     => 3,
                'state'         => 'active',
            ],
            // Roberto Chafloque (staff_id=5) - temporal activo
            [
                'staff_id'      => 5,
                'contract_type' => 'temporal',
                'date_start'    => '2026-01-01',
                'date_end'      => '2026-12-31',
                'salary'        => 2000.00,
                'probation'     => 1,
                'state'         => 'active',
            ],
            // Lucía Fernández (staff_id=6) - temporal activo
            [
                'staff_id'      => 6,
                'contract_type' => 'temporal',
                'date_start'    => '2026-03-01',
                'date_end'      => '2026-08-31',
                'salary'        => 2200.00,
                'probation'     => null,
                'state'         => 'active',
            ],
            // Pedro Sánchez (staff_id=7) - contrato expirado
            [
                'staff_id'      => 7,
                'contract_type' => 'temporal',
                'date_start'    => '2025-01-01',
                'date_end'      => '2025-12-31',
                'salary'        => 1800.00,
                'probation'     => null,
                'state'         => 'expired',
            ],
        ];

        foreach ($contracts as $contract) {
            DB::table('contracts')->insert(array_merge($contract, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
