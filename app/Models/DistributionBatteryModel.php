<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistributionBatteryModel extends Model
{
    //

    protected $table = 'distribution_battery';

    protected $fillable = [
        'dealer_id',
        'specification_no',
        'type_of_distribution',
        'created_by',
        'updated_by',
        'status',
    ];

    public function battery()
    {
        return $this->belongsTo(BatteryMastModel::class, 'specification_no', 'serial_no', 'MFD');
    }

    public function dealer()
    {
        return $this->belongsTo(DealerModel::class, 'dealer_id');
    }

    public function batteryRegs()
    {
        return $this->hasMany(BatteryRegModel::class, 'serialNo');
    }

    public function batteryMast()
    {
        return $this->belongsTo(BatteryMastModel::class, 'specification_no', 'serial_no');
    }

}
