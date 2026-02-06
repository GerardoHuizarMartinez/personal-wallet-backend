<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'first_last_name',
        'second_last_name',
        'birthday',
        'gender',
        'telephone',
        'cellphone',
        'country',
        'zipcode',
        'state',
        'municipality',
        'colony',
        'street',
        'no_ext',
        'no_int',
        'status',
        'url_image',
    ];

    
}
