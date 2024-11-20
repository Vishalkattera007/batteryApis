<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComplaintMasterModel extends Model
{
    protected $table = 'complaint_master';

    protected $fillable= [
        'customer_id',
        'Registered_battery_id',
        'complaint_raised_on',
        'complaint',
        'created_by',
        'updated_by'
    ];

    public function customer(){
        return $this->belongsTo(CustomerModel::class, 'customer_id', 'id');
    }

    public function batteryReg(){
        return $this->belongsTo(BatteryRegModel::class, 'Registered_battery_id', 'id');
    }
}
