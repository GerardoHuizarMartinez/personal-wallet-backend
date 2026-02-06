<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
      // $this->call(ColoniesSeeder::class);
       $this->call(CategoriesSeeder::class);
       $this->call(FinancingTypesSeeder::class);
       $this->call(PaymentMethodsSeeder::class);
       $this->call(UsersSeeder::class);
       $this->call(IncomesCategoriesSeeder::class);
       $this->call(IncomesSeeder::class);
       $this->call(PurchasesSeeder::class);

    }
}
