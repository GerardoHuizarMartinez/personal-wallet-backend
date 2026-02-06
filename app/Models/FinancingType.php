<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancingType extends Model
{
    protected $fillable = [
        'description',
        'number_months',
        'status',
        'created_at'
    ];
}
