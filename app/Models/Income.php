<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Category;
use App\Models\PaymentMethod;

class Income extends Model
{
    protected $fillable = [
        'product_name',
        'amount',
        'user_id',
        'category_id',
        'payment_method_id',
        'income_date',
        'comments',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo( Category::class, 'category_id');
    }

    public function payment_method()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }
}
