<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class dealerModel extends Model
{
    protected $table = 'dealer_master';
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'address',
        'adhar',
        'profileImage',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];


}
