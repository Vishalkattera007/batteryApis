<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class assignBatteryModel extends Model
{
    //
    protected $table = 'assigned_batteries';

    protected $fillable = [
        'dealer_id',
        'catergory_id',
        'sub_category_id',
        'nof_batteries',
        'created_by',
        'updated_by'
    ];
}
