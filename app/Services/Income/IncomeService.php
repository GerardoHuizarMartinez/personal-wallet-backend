<?php

namespace App\Services\Income;

use Illuminate\Support\Collection;
use App\Mappers\IncomeMapper;
use App\Models\Income;

class IncomeService  
{

    public function create(array $data): array
    {
        $income = Income::create([
            'product_name'      => $data['product_name'],
            'amount'            => $data['amount'],
            'category_id'       => $data['category_id'],
            'payment_method_id' => $data['payment_method_id'],
            'income_date'      => $data['income_date'],
            'comments'          => $data['comments'] ?? null,
            'user_id'           => 1, // temporal hasta tener auth
        ]);

        return IncomeMapper::toArray($income);
    }

   public function findCurrentMonthIncomes()
{
    $incomes = Income::with([
        'category',
    ])
        ->whereMonth('income_date', now()->month)
        ->whereYear('income_date', now()->year)
        ->orderBy('created_at', 'desc')
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