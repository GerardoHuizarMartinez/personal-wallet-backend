<?php

namespace App\Mappers;

use Illuminate\Support\Collection;

class ExpenseMapper
{
    public static function toDashboard(Collection $expenses): Collection
    {
        return $expenses->map(function ($expense) {

            return [

                'id' => $expense->id,

                'title' => $expense->product_name,

                'amount' => (float) $expense->amount,

                'type' => 'expense',

                'transactionDate' => $expense->expense_date,

                'currency' => 'MXN',

                'category' => [

                    'id' => $expense->category->id,

                    'name' => $expense->category->name,

                    'label' => $expense->category->label,

                ],

                'paymentMethod' => [

                    'id' => $expense->payment_method->id,

                    'name' => $expense->payment_method->name,

                    'label' => $expense->payment_method->label

                ],

            ];
        });
    }
}
