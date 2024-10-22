<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class batteryMastModel extends Model
{
    protected $table = 'battery_master';

    protected $fillable = [
        'serial_no',
        'category',
        'sub_category',
        'MFD',
        'created_by',
        'updated_by',
    ];
}
