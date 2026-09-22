<?php

namespace App\Services\Dashboard;

use Illuminate\Support\Collection;

class DashboardSummaryService
{
    public function build( Collection $monthPurchases, Collection $monthIncomes, Collection $yearPurchases, Collection $yearIncomes ): array
    {

    $expense = $monthPurchases->sum('amount');

    $income = $monthIncomes->sum('amount');

    $balance = $income - $expense;

    $yearExpense = $yearPurchases->sum('amount');

    $yearIncome = $yearIncomes->sum('amount');

    $yearBalance = $yearIncome - $yearExpense;

    $purchasesCount = $monthPurchases->count();

    $incomesCount = $monthIncomes->count();

    $transactions = $purchasesCount + $incomesCount;

    return [

        'income' => $income,

        'expense' => $expense,

        'balance' => $balance,

        'yearIncome' => $yearIncome,

        'yearExpense' => $yearExpense,

        'yearBalance' => $yearBalance,

        'transactions' => $transactions,

        'purchasesCount' => $purchasesCount,

        'incomesCount' => $incomesCount,

    ];
    }
}