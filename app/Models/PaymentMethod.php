<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $fillable = [
        'method',
        'icon',
        'created_at'
    ];

    public function purchases()
    {
        return $this->hasMany(Purchase::class,'payment_method_id');
    }
}
