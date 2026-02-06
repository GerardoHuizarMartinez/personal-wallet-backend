<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\IncomeCategory;


class IncomesCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        IncomeCategory::create([
            'name' => 'Pago de semana',
            'status' => 'Activo'
        ]);
        IncomeCategory::create([
            'name' => 'Pago de quincena',
            'status' => 'Activo'
        ]);
        IncomeCategory::create([
            'name' => 'Prestamo',
            'status' => 'Activo'
        ]);
        IncomeCategory::create([
            'name' => 'Finiquito',
            'status' => 'Activo'
        ]);
        IncomeCategory::create([
            'name' => 'Cundina',
            'status' => 'Activo'
        ]);
        IncomeCategory::create([
            'name' => 'Cobro de deuda',
            'status' => 'Activo'
        ]);
        IncomeCategory::create([
            'name' => 'Aguinaldo',
            'status' => 'Activo'
        ]);
        IncomeCategory::create([
            'name' => 'Utilidades',
            'status' => 'Activo'
        ]);
        IncomeCategory::create([
            'name' => 'Otro',
            'status' => 'Activo'
        ]);
    }
}
