<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;

class PaymentMethodsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PaymentMethod::create(['name' => 'Efectivo', 'label' => 'cash', 'created_at' => '2024-10-15 13:00:00',]);
        PaymentMethod::create(['name' => 'Transferencia', 'label' => 'transfer', 'created_at' => '2024-10-15 13:00:00',]);
        PaymentMethod::create(['name' => 'Tarjeta de débito', 'label' => 'debit_card', 'created_at' => '2024-10-15 13:00:00',]);
        PaymentMethod::create(['name' => 'Tarjeta de crédito', 'label' => 'credit-card', 'created_at' => '2024-10-15 13:00:00',]);
    }
}