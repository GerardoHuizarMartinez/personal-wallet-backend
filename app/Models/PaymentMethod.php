<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Expense;
use App\Models\Income;

class PaymentMethod extends Model
{
    protected $fillable = [
        'name',
        'label',
        'icon',
    ];

    public function expenses()
    {
        return $this->hasMany(Expense::class,'payment_method_id');
    }

        public function incomes()
    {
        return $this->hasMany(Income::class,'payment_method_id');
    }
}