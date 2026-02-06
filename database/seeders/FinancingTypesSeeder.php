<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\FinancingType;

class FinancingTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        FinancingType::create([
            'description' => 'Contado',
            'number_months' => 0,
            'status' => 'Activo',
            'created_at' => '2024-10-15 13:00:00',
        ]);
        FinancingType::create([
            'description' => '1 Mes',
            'number_months' => 1,
            'status' => 'Activo',
            'created_at' => '2024-10-15 13:00:00',
        ]);
        FinancingType::create([
            'description' => '3 Meses',
            'number_months' => 3,
            'status' => 'Activo',
            'created_at' => '2024-10-15 13:00:00',
        ]);
        FinancingType::create([
            'description' => '4 Meses',
            'number_months' => 4,
            'status' => 'Activo',
            'created_at' => '2024-10-15 13:00:00',
        ]);
        FinancingType::create([
            'description' => '6 Meses',
            'number_months' => 6,
            'status' => 'Activo',
            'created_at' => '2024-10-15 13:00:00',
        ]);
        FinancingType::create([
            'description' => '9 Meses',
            'number_months' => 9,
            'status' => 'Activo',
            'created_at' => '2024-10-15 13:00:00',
        ]);
        FinancingType::create([
            'description' => '12 Meses',
            'number_months' => 12,
            'status' => 'Activo',
            'created_at' => '2024-10-15 13:00:00',
        ]);
        FinancingType::create([
            'description' => '18 Meses',
            'number_months' => 18,
            'status' => 'Activo',
            'created_at' => '2024-10-15 13:00:00',
        ]);
        FinancingType::create([
            'description' => '24 Meses',
            'number_months' => 24,
            'status' => 'Activo',
            'created_at' => '2024-10-15 13:00:00',
        ]);
    }
}
