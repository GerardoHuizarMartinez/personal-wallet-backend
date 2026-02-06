<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstallmentPayment extends Model
{
    protected $fillable = [
        'installment_id',
        'amount_paid',
        'payment_date',
        'payment_method',
        'comments',
    ];
}
