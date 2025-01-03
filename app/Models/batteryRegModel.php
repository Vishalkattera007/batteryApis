<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BatteryRegModel extends Model
{
    protected $table = "battery_reg";

    protected $fillable = [
            'id',
            'serialNo',
            'type',
            'modelNumber',
            // 'firstName',
            // 'lastName',
            // 'email',
            // 'pincode',
            // 'mobileNumber',
            'BPD',//Battery Purchased Date
            'VRN', //Vehicle Registarion Number
            'warranty',
            'prowarranty',
            'customer_id',
            'Acceptance',
            'created_by',
            'updated_by'
    ];

    public function customer(){
        return $this->belongsTo(CustomerModel::class, 'customer_id');
    }

    public function distribution()
    {
        return $this->belongsTo(DistributionBatteryModel::class, 'specification_no', 'serialNo');
    }
}
