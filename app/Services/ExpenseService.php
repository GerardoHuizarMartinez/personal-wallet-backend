<?php

namespace App\Services;

use App\Models\Expense;
use App\Mappers\ExpenseMapper;
use Illuminate\Support\Collection;


class ExpenseService
{
    public function create(array $data): array
    {
        $expense = Expense::create([
            'product_name'      => $data['product_name'],
            'amount'            => $data['amount'],
            'category_id'       => $data['category_id'],
            'payment_method_id' => $data['payment_method_id'],
            'expense_date'      => $data['expense_date'],
            'comments'          => $data['comments'] ?? null,
            'user_id'           => 1, // temporal hasta tener auth
        ]);

        return ExpenseMapper::toArray($expense);
    } 

    public function findCurrentMonthExpenses()
    {
        $expenses = Expense::with([
            'category',
            'payment_method',
        ])
            ->whereMonth('expense_date', now()->month)
            ->whereYear('expense_date', now()->year)
            ->orderBy('created_at', 'desc')
            ->get();

        return ExpenseMapper::toDashboard($expenses);
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

}
