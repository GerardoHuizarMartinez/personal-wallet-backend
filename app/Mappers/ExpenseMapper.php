<?php

namespace App\Mappers;

use App\Models\Expense;
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

                'comments' => $expense->comments,
                
                'created_at' => $expense->created_at,

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

    public static function toArray(Expense $expense): array
    {
        return [
            'id'                => $expense->id,
            'product_name'      => $expense->product_name,
            'amount'            => (float) $expense->amount,
            'category_id'       => $expense->category_id,
            'payment_method_id' => $expense->payment_method_id,
            'expense_date'      => $expense->expense_date,
            'comments'          => $expense->comments,
            'created_at'        => $expense->created_at,
        ];
    }
}
