<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Authenticatable;


class dealerModel extends Model implements AuthenticatableContract
{
    use Authenticatable, HasFactory; // Add HasApiTokens here
    use HasFactory;


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
