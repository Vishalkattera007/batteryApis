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
        'updated_by'
    ];

    public function battery()
    {
        return $this->belongsTo(BatteryMastModel::class, 'specification_no', 'serial_no','MFD');
    }
}
