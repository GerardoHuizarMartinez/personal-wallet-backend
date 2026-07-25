<?php

namespace App\Services\Dashboard;

use Illuminate\Support\Collection;

class DashboardSummaryService
{
    public function build( Collection $purchases, Collection $incomes ): array 
    {
    
    $expense = $purchases->sum('amount');

    $income = $incomes->sum('amount');

    $balance = $income - $expense;

    $transactions = $purchases->count() + $incomes->count();

    return [

        'income' => $income,

        'expense' => $expense,

        'balance' => $balance,

        'transactions' => $transactions,

    ];
    }
}