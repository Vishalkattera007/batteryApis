<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class batteryRegModel extends Model
{
    //

    protected $table = "battery_reg";

    protected $fillable = [
            'serialNo',
            'type',
            'firstName',
            'lastName',
            'pincode',
            'mobileNumber',
            'BPD',//Battery Purchased Date
            'VRN', //Vehicle Registarion Number
            'Acceptance',
            'created_by',
            'updated_by'
    ];

}
