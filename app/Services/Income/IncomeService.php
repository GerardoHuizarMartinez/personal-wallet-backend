<?php

namespace App\Services\Income;

use Illuminate\Support\Collection;
use App\Mappers\IncomeMapper;
use App\Models\Income;

class IncomeService  
{
   public function findCurrentMonthIncomes()
{
    $incomes = Income::with([
        'category',
    ])
        // ->whereMonth('income_date', now()->month)
        // ->whereYear('income_date', now()->year)
        ->whereMonth('income_date', 07)
        ->whereYear('income_date', 2025)
        ->orderByDesc('income_date')
        ->get();

    //  dd($incomes);

    return IncomeMapper::toDashboard($incomes);
}

public function findYearlyIncomes(?int $year = null): Collection
{
    $year ??= now()->year;

    return Income::query()

        ->selectRaw('MONTH(income_date) as month')

        ->selectRaw('SUM(amount) as total')

        ->whereYear('income_date', $year)

        ->groupBy('month')

        ->orderBy('month')

        ->get();
}

public function getMonthlyIncomes()
{
    $incomes = Income::query()
        ->selectRaw('MONTH(income_date) as month')
        ->selectRaw('SUM(amount) as total')
        ->whereYear('income_date', 2025)
        ->groupBy('month')
        ->orderBy('month')
        ->get()
        ->keyBy('month');

    return collect(range(1, 12))->map(function ($month) use ($incomes) {
        return [
            'month' => $month,
            'total' => (float) ($incomes->get($month)?->total ?? 0),
        ];
    });
}



}