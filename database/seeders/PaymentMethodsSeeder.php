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
        PaymentMethod::create(['method' => 'Efectivo', 'icon' => 'payments', 'created_at' => '2024-10-15 13:00:00',]);
        PaymentMethod::create(['method' => 'Transferencia', 'icon' => 'swap-horiz', 'created_at' => '2024-10-15 13:00:00',]);
        PaymentMethod::create(['method' => 'Tarjeta de débito', 'icon' => 'credit-card', 'created_at' => '2024-10-15 13:00:00',]);
        PaymentMethod::create(['method' => 'Tarjeta de crédito', 'icon' => 'credit-card', 'created_at' => '2024-10-15 13:00:00',]);
    }
}