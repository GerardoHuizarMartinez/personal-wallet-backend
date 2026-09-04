<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Colony extends Model
{
   use SoftDeletes;

   protected $fillable = [
        'zipcode',
        'name',
        'settlement_type_code',
        'settlement_type',
        'city_code',
        'city',
        'state_code',
        'state',
        'office_code',
        'zone',
        
    ];
}
