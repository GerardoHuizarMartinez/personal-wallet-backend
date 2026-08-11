<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
use App\Models\PaymentMethod;
use Illuminate\Database\Eloquent\SoftDeletes;


use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
        use SoftDeletes;

    protected $fillable = [
        'product_name',
        'amount',
        'user_id',
        'category_id',
        'payment_method_id',
        'expense_date',
        'comments'
    ];


    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function payment_method()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }
}
