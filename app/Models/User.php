<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class User extends Model
{
    use HasApiTokens, SoftDeletes;

    protected $fillable = [
        'name',
        'first_last_name',
        'second_last_name',
        'birthday',
        'gender',
        'email',
        'password',
        'telephone',
        'cellphone',
        'country',
        'colony_id',
        'street',
        'no_ext',
        'no_int',
        'status',
        'url_image',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function colony()
    {
        return $this->belongsTo(Colony::class);
    }
}
