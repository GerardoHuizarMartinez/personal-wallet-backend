<?php

namespace App\Services\Dashboard;

use Illuminate\Support\Collection;

class DashboardTransactionService
{
    public function build(Collection $purchases, Collection $incomes): Collection 
    {
        return collect()->merge($purchases)->merge($incomes)->sortByDesc('transactionDate')->values();
    }

}