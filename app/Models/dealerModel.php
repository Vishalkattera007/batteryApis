<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Authenticatable;


class DealerModel extends Model implements AuthenticatableContract
{
    use Authenticatable, HasFactory; // Add HasApiTokens here
    use HasFactory;


    protected $table = 'dealer_master';
    protected $fillable = [
        'dealerId',
        'FirstName',
        'LastName',
        'email',
        'password',
        'phone_number',
        'address',
        'state',
        'pincode',
        'firmRegNo',
        'pancard',
        'profileImage',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];


}
