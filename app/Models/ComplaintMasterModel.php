<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComplaintMasterModel extends Model
{
    protected $table = 'complaint_master';

    protected $fillable= [
        'complaintId',
        'customer_id',
        'Registered_battery_id',
        'replaced_battery_id',
        'complaint_raised_on',
        'complaint',
        'created_by',
        'status',
        'remarks',
        'status_updated_By',
        'status_updated_On',
        'updated_by'
    ];

    public function customer(){
        return $this->belongsTo(CustomerModel::class, 'customer_id', 'id');
    }

    public function batteryReg(){
        return $this->belongsTo(batteryRegModel::class, 'Registered_battery_id', 'id');
    }

    public function dealer(){
        return $this->belongsTo(DealerModel::class, 'created_by', 'id');
    }

    
    public function replaceBattery(){
        return $this->belongsTo(batteryRegModel::class, 'replaced_battery_id', 'id');
    }
    
}
