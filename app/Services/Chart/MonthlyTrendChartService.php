<?php

namespace App\Services\Charts;

use Illuminate\Support\Collection;

class MonthlyTrendChartService
{
    public function build(
        Collection $expenses,
        Collection $incomes
    ): Collection {

        $expenses = $expenses->keyBy('month');
        $incomes = $incomes->keyBy('month');

        return collect(range(1, 12))
            ->map(function ($month) use ($expenses, $incomes) {

                return [

                    'month' => $month,

                    'expense' => $expenses[$month]['total'] ?? 0,

                    'income' => $incomes[$month]['total'] ?? 0,

                ];

            });

    }
}