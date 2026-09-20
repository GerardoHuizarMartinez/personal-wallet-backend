<?php

namespace App\Services\Dashboard;

use Illuminate\Support\Collection;

class DashboardSummaryService
{
    public function build( Collection $yearPurchases, Collection $yearIncomes, Collection $monthPurchases, Collection $monthIncomes ): array
    {

    $expense = $yearPurchases->sum('amount');

    $income = $yearIncomes->sum('amount');

    $balance = $income - $expense;

    $purchasesCount = $monthPurchases->count();

    $incomesCount = $monthIncomes->count();

    $transactions = $purchasesCount + $incomesCount;

    return [

        'income' => $income,

        'expense' => $expense,

        'balance' => $balance,

        'transactions' => $transactions,

        'purchasesCount' => $purchasesCount,

        'incomesCount' => $incomesCount,

    ];
    }
}