<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class adminmaster extends Model
{
    //
    use HasFactory;

    protected $table = 'admin_master';

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

}
