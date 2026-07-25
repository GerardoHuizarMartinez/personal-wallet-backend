<?php

namespace App\Services\Purchase;

use App\Mappers\ExpenseMapper;
use Illuminate\Support\Collection;;
use App\Models\Expense;

class PurchaseService
{
    public function findCurrentMonthPurchases()
    {
        $purchases = Expense::with([
            'category',
            'payment_method',
        ])
            ->whereMonth('expense_date', '07')
            ->whereYear('expense_date', 2025)
            // ->whereMonth('expense_date', now()->month)
            // ->whereYear('expense_date', now()->year)
            ->orderByDesc('expense_date')
            ->get();

        return ExpenseMapper::toDashboard($purchases);
    }

    public function findYearlyExpenses(?int $year = null): Collection
    {
        $year ??= now()->year;

        return Expense::query()

            ->selectRaw('MONTH(expense_date) as month')

            ->selectRaw('SUM(amount) as total')

            ->whereYear('expense_date', $year)

            ->groupBy('month')

            ->orderBy('month')

            ->get();
    }

    public function getMonthlyExpenses()
    {
        $expenses = Expense::query()
            ->selectRaw('MONTH(expense_date) as month')
            ->selectRaw('SUM(total_price) as total')
            ->whereYear('expense_date', 2025)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        return collect(range(1, 12))->map(function ($month) use ($expenses) {
            return [
                'month' => $month,
                'total' => (float) ($expenses->get($month)?->total ?? 0),
            ];
        });
    }
    public function findExpensesByCategory(?int $year = null): Collection
    {
        $year ??= now()->year;

        return Expense::query()

            ->join(
                'categories',
                'categories.id',
                '=',
                'purchases.category_id'
            )

            ->selectRaw('categories.id')
            ->selectRaw('categories.name')
            ->selectRaw('SUM(total_price) as total')

            ->whereYear('expense_date', $year)

            ->groupBy(
                'categories.id',
                'categories.name'
            )

            ->orderByDesc('total')

            ->get();
    }

    // public function findExpensesByPaymentMethod(?int $year = null): Collection {}
}
