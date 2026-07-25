<?php

namespace App\Services\Charts;

use Illuminate\Support\Collection;

class ExpenseCategoryChartService
{
    public function build(Collection $categories): Collection
    {
        $totalExpenses = $categories->sum('total');

        return $categories
            ->map(function ($category) use ($totalExpenses) {

                $percentage = $totalExpenses > 0 ? round(($category->total / $totalExpenses) * 100, 2) : 0;

                return [

                    'id' => $category->id,

                    'label' => $category->name,

                    'value' => (float) $category->total,

                    'percentage' => $percentage,

                ];
            })
            ->values();
    }
}