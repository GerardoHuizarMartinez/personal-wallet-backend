<?php

namespace App\Services\Dashboard;

use Illuminate\Support\Collection;

class DashboardTransactionService
{
    public function build(Collection $purchases, Collection $incomes): Collection
    {
        return collect()
            ->merge($purchases)
            ->merge($incomes)
            ->sort(function ($a, $b) {
                if ($a['transactionDate'] === $b['transactionDate']) {
                    return $b['created_at'] <=> $a['created_at'];
                }
                return $b['transactionDate'] <=> $a['transactionDate'];
            })
            ->values();
    }
}
