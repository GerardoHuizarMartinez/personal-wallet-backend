<?php

namespace App\Services\Dashboard;

use Illuminate\Support\Collection;
use App\Services\Income\IncomeService;
use App\Services\Purchase\PurchaseService;
//use App\Services\Chart\App\Services\Charts\MonthlyTrendChartService;
use App\Services\Dashboard\DashboardSummaryService;
use App\Services\Dashboard\DashboardTransactionService;

class DashboardService
{


    public function __construct(
        private PurchaseService $purchaseService,

        private IncomeService $incomeService,

        private DashboardSummaryService $summaryService,

        private DashboardTransactionService $transactionService,

      //  private ChartsMonthlyTrendChartService $monthlyTrendChartService,
    ) {}

    public function getDashboard(): array
    {
        /*
        |------------------------------------------
        | Data Sources
        |------------------------------------------
        */

        $purchases = $this->purchaseService->findCurrentMonthPurchases();

        $incomes = $this->incomeService->findCurrentMonthIncomes();

        $yearlyExpenses = $this->purchaseService->findYearlyExpenses();

        $yearlyIncomes = $this->incomeService->findYearlyIncomes();

        /*
        |------------------------------------------
        | Dashboard Widgets
        |------------------------------------------
        */

        $summary = $this->summaryService->build( $purchases, $incomes );

        $transactions = $this->transactionService->build($purchases, $incomes);

        // dd($transactions);

        //$monthlyTrend = $this->monthlyTrendChartService->build( $yearlyExpenses, $yearlyIncomes );

        return [
            'summary' => $summary,
            'recentTransactions' => $transactions,
         //   'monthlyTrend' => $monthlyTrend,
            'expensesByCategory' => [],
            'expensesByPaymentMethod' => [],
        ];
    }

    private function buildSummary(Collection $transactions): array
    {
        $expense = $transactions
            ->where('type', 'expense')
            ->sum('amount');

        $income = $transactions
            ->where('type', 'income')
            ->sum('amount');

        return [

            'income' => (float) $income,
            'expense' => (float) $expense,
            'balance' => (float) ($income - $expense),
            'transactions' => $transactions->count(),

        ];
    }

    private function buildMonthlyTrend( Collection $monthlyExpenses, Collection $monthlyIncomes ): array {
        return [
            'labels' => [
                'Enero',
                'Febrero',
                'Marzo',
                'Abril',
                'Mayo',
                'Junio',
                'Julio',
                'Agosto',
                'Septiembre',
                'Octubre',
                'Noviembre',
                'Diciembre',
            ],

            'income' => $monthlyIncomes
                ->pluck('total')
                ->values(),

            'expense' => $monthlyExpenses
                ->pluck('total')
                ->values(),
        ];
    }

}
