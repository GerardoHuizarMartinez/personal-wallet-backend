<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Income extends Model
{
    protected $fillable = [
        'user_id',
        'income_category_id',
        'amount',
        'income_date',
        'payment_method',
        'comments'
    ];
}
