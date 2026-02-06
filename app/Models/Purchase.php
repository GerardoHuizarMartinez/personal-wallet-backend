<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchase extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'product_name',
        'total_price',
        'user_id',
        'category_id',
        'financing_type_id',
        'financing_method',
        'payment_method_id',
        'comments',
        'purchase_date',
    ];

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function payment_method()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }
}
