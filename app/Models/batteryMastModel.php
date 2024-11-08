<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BatteryMastModel extends Model
{
    // Define the corresponding table name
    protected $table = 'battery_master';

    // Fillable attributes for mass assignment
    protected $fillable = [
        'serial_no',
        'categoryId',
        'sub_category',
        'MFD',                // Manufacturer's Date
        'warranty_period',
        'prowarranty_period',
        'status',
        'created_by',
        'updated_by',
    ];

    public function category()
    {
        return $this->belongsTo(categoryModel::class, 'categoryId','id');
    }

    public function subCategory()
    {
        return $this->belongsTo(subCategoryModel::class, 'sub_category', 'id');
    }

}
