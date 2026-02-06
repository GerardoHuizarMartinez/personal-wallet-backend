<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Installment extends Model
{
      protected $fillable = [
        'purchase_id',
        'description',
        'installment_number',
        'amount',
        'due_date',
        'status',
    ];
}
