<?php

namespace App\Mappers;

use App\Models\Income;
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

                'created_at' => $incomes->created_at,

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

      public static function toArray(Income $income): array
    {
        return [
            'id'                => $income->id,
            'product_name'      => $income->product_name,
            'amount'            => (float) $income->amount,
            'category_id'       => $income->category_id,
            'payment_method_id' => $income->payment_method_id,
            'income_date'      => $income->income_date,
            'comments'          => $income->comments,
            'created_at'        => $income->created_at,
        ];
    }
}
