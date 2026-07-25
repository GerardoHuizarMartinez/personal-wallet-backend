<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'label',
        'type',
        'status',
        'icon',
        'created_at',
    ];

    
}
