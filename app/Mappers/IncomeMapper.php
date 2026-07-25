<?php

namespace App\Mappers;

use Illuminate\Support\Collection;

class IncomeMapper
{
    public static function toDashboard(Collection $incomes): Collection
    {
        return $incomes->map(function ($incomes) {

            return [

                'id' => $incomes->id,

                'title' => $incomes->product_name,

                'amount' => (float) $incomes->amount,

                'type' => 'income',

                'transactionDate' => $incomes->income_date,

                'currency' => 'MXN',

                'category' => [

                    'id' => $incomes->category->id,

                    'name' => $incomes->category->name,

                    'label' => $incomes->category->label,

                ],

                'paymentMethod' => [

                    'id' => $incomes->payment_method->id,

                    'name' => $incomes->payment_method->name,

                    'label' => $incomes->payment_method->label

                ],

            ];
        });
    }
}
