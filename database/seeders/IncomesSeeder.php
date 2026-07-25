<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Income;

class IncomesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Income::create([
            'product_name' => 'Primer registro de pago',
            'amount' => 548.36,
            'user_id' => 1,
            'category_id' => 32,
            'payment_method_id' => 1,
            'income_date' => '2025-07-13',
            'comments' => 'Primer registro de prueba de un ingreso ficticio',
            'created_at' => '2024-10-15 13:00:00',
        ]);
        Income::create([
            'product_name' => 'Segundo registro de pago',
            'amount' => 1236.36,
            'user_id' => 1,
            'category_id' => 32,
            'payment_method_id' => 1,
            'income_date' => '2025-07-13',
            'comments' => 'Me lo encontre entre mis bolsillos',
            'created_at' => '2024-10-15 13:00:00',
        ]);
        Income::create([
            'product_name' => 'Tercer registro de pago',
            'amount' => 548.17,
            'user_id' => 1,
            'category_id' => 32,
            'payment_method_id' => 1,
            'income_date' => '2025-07-13',
            'comments' => null,
            'created_at' => '2024-10-15 13:00:00',
        ]);
    }
}
