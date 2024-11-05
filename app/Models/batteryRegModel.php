<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BatteryRegModel extends Model
{
    protected $table = "battery_reg";

    protected $fillable = [
            'serialNo',
            'type',
            // 'firstName',
            // 'lastName',
            // 'email',
            // 'pincode',
            // 'mobileNumber',
            'BPD',//Battery Purchased Date
            'VRN', //Vehicle Registarion Number
            'warranty',
            'customer_id',
            'Acceptance',
            'created_by',
            'updated_by'
    ];

    public function customer(){
        return $this->belongsTo(CustomerModel::class, 'customer_id');
    }

}
